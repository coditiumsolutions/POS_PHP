<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $customerType = $request->get('customer_type');

        $query = SalesInvoice::with('customer')
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo)
            ->where('invoice_status', '!=', 'Cancelled');

        if ($customerType === 'walkin') {
            $query->whereHas('customer', function($q) {
                $q->where('customer_code', 'C001')
                  ->orWhere('customer_name', 'like', '%Walk-in%');
            });
        } elseif ($customerType === 'credit') {
            $query->whereHas('customer', function($q) {
                $q->where('customer_code', '!=', 'C001')
                  ->where('customer_name', 'not like', '%Walk-in%');
            })->where(function($q) {
                $q->whereNull('remarks')
                  ->orWhere('remarks', 'not like', 'Monthly Supply%');
            });
        } elseif ($customerType === 'monthly') {
            $query->where('remarks', 'like', 'Monthly Supply%');
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->get();
        
        $summary = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('net_amount'),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_balance' => $invoices->sum('balance_amount'),
        ];

        return view('reports.sales', compact('invoices', 'summary', 'dateFrom', 'dateTo', 'customerType'));
    }

    public function purchases(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $invoices = PurchaseInvoice::with('vendor')
            ->whereDate('purchase_date', '>=', $dateFrom)
            ->whereDate('purchase_date', '<=', $dateTo)
            ->where('invoice_status', '!=', 'Cancelled')
            ->orderBy('purchase_date', 'desc')
            ->get();

        $summary = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('net_amount'),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_balance' => $invoices->sum('balance_amount'),
        ];

        return view('reports.purchases', compact('invoices', 'summary', 'dateFrom', 'dateTo'));
    }

    public function expenses(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $categoryUid = $request->get('category_uid');

        $query = Expense::with(['category', 'branch', 'user'])
            ->whereDate('expense_date', '>=', $dateFrom)
            ->whereDate('expense_date', '<=', $dateTo);

        if ($categoryUid) {
            $query->where('expense_category_uid', $categoryUid);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $totalAmount = $expenses->sum('amount');

        $categories = DB::table('expense_categories')
            ->where('is_active', true)
            ->orderBy('category_name')
            ->get();

        return view('reports.expenses', compact('expenses', 'totalAmount', 'dateFrom', 'dateTo', 'categories', 'categoryUid'));
    }

    public function inventory(Request $request)
    {
        $search = $request->get('search');
        $lowStockOnly = $request->get('low_stock');

        $query = DB::table('inventories as i')
            ->join('products as p', 'i.product_uid', '=', 'p.uid')
            ->leftJoin('warehouses as w', 'i.warehouse_uid', '=', 'w.uid')
            ->leftJoin('product_categories as c', 'p.category_uid', '=', 'c.uid')
            ->select(
                'p.product_code',
                'p.product_name',
                'c.category_name',
                'w.warehouse_name',
                'i.quantity_in_stock',
                'i.quantity_available',
                'p.minimum_stock',
                'p.sale_price',
                DB::raw('i.quantity_in_stock * p.sale_price as stock_value')
            )
            ->where('p.is_active', true);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('p.product_name', 'like', "%{$search}%")
                  ->orWhere('p.product_code', 'like', "%{$search}%");
            });
        }

        if ($lowStockOnly) {
            $query->whereRaw('i.quantity_in_stock <= p.minimum_stock');
        }

        $items = $query->orderBy('p.product_name')->get();
        $totalValue = $items->sum('stock_value');

        return view('reports.inventory', compact('items', 'totalValue', 'search', 'lowStockOnly'));
    }

    public function branch(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $branch = $request->get('branch');

        $query = SalesInvoice::with('branch')
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo)
            ->where('invoice_status', '!=', 'Cancelled');

        if ($branch) {
            $query->where('branch', $branch);
        }

        $sales = $query->select('branch', DB::raw('COUNT(*) as invoice_count'), 
                                DB::raw('SUM(net_amount) as total_amount'),
                                DB::raw('SUM(paid_amount) as total_paid'))
            ->groupBy('branch')
            ->get();

        $branches = DB::table('sales_invoices')
            ->select('branch')
            ->distinct()
            ->whereNotNull('branch')
            ->orderBy('branch')
            ->pluck('branch');

        return view('reports.branch', compact('sales', 'dateFrom', 'dateTo', 'branches', 'branch'));
    }

    public function user(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $userName = $request->get('user_name');

        $query = SalesInvoice::with('user')
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo)
            ->where('invoice_status', '!=', 'Cancelled');

        if ($userName) {
            $query->where('user_name', $userName);
        }

        $sales = $query->select('user_name', DB::raw('COUNT(*) as invoice_count'), 
                                DB::raw('SUM(net_amount) as total_amount'),
                                DB::raw('SUM(paid_amount) as total_paid'))
            ->groupBy('user_name')
            ->get();

        $users = DB::table('users')
            ->where('is_active', true)
            ->orderBy('user_name')
            ->pluck('user_name');

        return view('reports.user', compact('sales', 'dateFrom', 'dateTo', 'users', 'userName'));
    }
}
