<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use App\Models\EmployeePayment;
use App\Models\VendorPayment;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    public function salesItems(Request $request)
    {
        $dateFrom = $this->parseReportDate($request->get('date_from'), now()->startOfMonth());
        $dateTo = $this->parseReportDate($request->get('date_to'), now());
        $branch = trim((string) $request->get('branch', ''));

        $query = DB::table('sales_invoice_details as d')
            ->join('sales_invoices as s', 'd.sales_invoice_uid', '=', 's.uid')
            ->leftJoin('products as p', 'd.product_uid', '=', 'p.uid')
            ->whereDate('s.invoice_date', '>=', $dateFrom)
            ->whereDate('s.invoice_date', '<=', $dateTo)
            ->where('s.invoice_status', '!=', 'Cancelled');

        if ($branch !== '') {
            $query->where('s.branch', $branch);
        }

        $summaryRow = (clone $query)
            ->selectRaw('COUNT(*) as line_count, COALESCE(SUM(d.quantity), 0) as qty, COALESCE(SUM(d.total_amount), 0) as amount')
            ->first();

        $summary = [
            'lines' => (int) ($summaryRow->line_count ?? 0),
            'qty' => (float) ($summaryRow->qty ?? 0),
            'amount' => (float) ($summaryRow->amount ?? 0),
        ];

        $items = $query
            ->select(
                'd.uid as detail_uid',
                's.uid as sales_invoice_uid',
                's.invoice_no',
                's.invoice_date',
                's.branch',
                'p.product_code',
                'p.product_name',
                'd.quantity',
                'd.unit_price',
                'd.total_amount'
            )
            ->orderByDesc('s.invoice_date')
            ->orderBy('s.invoice_no')
            ->paginate(50)
            ->withQueryString();

        $branches = Branch::where('is_active', true)
            ->orderBy('branch_name')
            ->pluck('branch_name');

        // Also include any branch names already used on invoices.
        $invoiceBranches = DB::table('sales_invoices')
            ->whereNotNull('branch')
            ->where('branch', '!=', '')
            ->distinct()
            ->orderBy('branch')
            ->pluck('branch');

        $branches = $branches->merge($invoiceBranches)->unique()->sort()->values();

        $dateFromDisplay = Carbon::parse($dateFrom)->format('d-M-Y');
        $dateToDisplay = Carbon::parse($dateTo)->format('d-M-Y');

        return view('reports.sales-items', compact(
            'items',
            'summary',
            'dateFrom',
            'dateTo',
            'dateFromDisplay',
            'dateToDisplay',
            'branches',
            'branch'
        ));
    }

    private function parseReportDate(?string $value, Carbon $fallback): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return $fallback->format('Y-m-d');
        }

        foreach (['d-M-Y', 'Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                // try next format
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $fallback->format('Y-m-d');
        }
    }

    public function daily(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $posType = trim((string) $request->get('pos_type', ''));
        $allowedPosTypes = ['Walk-In', 'Credit', 'Monthly'];

        $query = SalesInvoice::with(['paymentMethod', 'customer'])
            ->whereDate('invoice_date', $date)
            ->where('invoice_status', '!=', 'Cancelled');

        if ($posType !== '' && in_array($posType, $allowedPosTypes, true)) {
            $query->where('pos_type', $posType);
        }

        $invoices = $query->orderBy('invoice_date')->get();

        $summary = [
            'invoices' => $invoices->count(),
            'sales' => (float) $invoices->sum('net_amount'),
            'paid' => (float) $invoices->sum('paid_amount'),
            'balance' => (float) $invoices->sum('balance_amount'),
        ];

        return view('reports.daily', compact('invoices', 'summary', 'date', 'posType', 'allowedPosTypes'));
    }

    public function payments(Request $request)
    {
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);

        $rows = SalesInvoice::with('paymentMethod')
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo)
            ->where('invoice_status', '!=', 'Cancelled')
            ->where('paid_amount', '>', 0)
            ->orderByDesc('invoice_date')
            ->limit(1000)
            ->get();

        $byMethod = $rows->groupBy(fn ($r) => optional($r->paymentMethod)->payment_method_name ?: 'Unknown')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'amount' => (float) $group->sum('paid_amount'),
            ]);

        return view('reports.payments', compact('rows', 'byMethod', 'dateFrom', 'dateTo', 'dateFromDisplay', 'dateToDisplay'));
    }

    public function vendorPayments(Request $request)
    {
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);

        $payments = DB::table('vendor_payments as vp')
            ->leftJoin('vendors as v', 'vp.vendor_uid', '=', 'v.uid')
            ->whereDate('vp.payment_date', '>=', $dateFrom)
            ->whereDate('vp.payment_date', '<=', $dateTo)
            ->select(
                'vp.uid',
                'vp.payment_no',
                'vp.payment_date',
                'vp.amount',
                'vp.payment_mode',
                'vp.remarks',
                'v.vendor_name'
            )
            ->orderByDesc('vp.payment_date')
            ->limit(1000)
            ->get();

        $total = (float) $payments->sum('amount');

        return view('reports.vendor-payments', compact('payments', 'total', 'dateFrom', 'dateTo', 'dateFromDisplay', 'dateToDisplay'));
    }

    public function terminal(Request $request)
    {
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);

        $branch = trim((string) $request->get('branch', ''));
        $terminal = trim((string) $request->get('terminal', ''));
        $userName = trim((string) $request->get('user_name', ''));

        $baseQuery = SalesInvoice::query()
            ->whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo)
            ->where('invoice_status', '!=', 'Cancelled');

        if ($branch !== '') {
            $baseQuery->where('branch', $branch);
        }
        if ($terminal !== '') {
            $baseQuery->where('terminal', $terminal);
        }
        if ($userName !== '') {
            $baseQuery->where('user_name', $userName);
        }

        $rows = (clone $baseQuery)
            ->select(
                'branch',
                'terminal',
                'user_name',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(net_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as total_paid')
            )
            ->groupBy('branch', 'terminal', 'user_name')
            ->orderBy('branch')
            ->orderBy('terminal')
            ->orderBy('user_name')
            ->get();

        $branches = SalesInvoice::query()
            ->whereNotNull('branch')
            ->where('branch', '!=', '')
            ->distinct()
            ->orderBy('branch')
            ->pluck('branch');

        $terminals = SalesInvoice::query()
            ->whereNotNull('terminal')
            ->where('terminal', '!=', '')
            ->distinct()
            ->orderBy('terminal')
            ->pluck('terminal');

        $users = SalesInvoice::query()
            ->whereNotNull('user_name')
            ->where('user_name', '!=', '')
            ->distinct()
            ->orderBy('user_name')
            ->pluck('user_name');

        $summary = [
            'invoices' => (int) $rows->sum('invoice_count'),
            'sales' => (float) $rows->sum('total_amount'),
            'paid' => (float) $rows->sum('total_paid'),
        ];

        return view('reports.terminal', compact(
            'rows',
            'summary',
            'dateFrom',
            'dateTo',
            'dateFromDisplay',
            'dateToDisplay',
            'branches',
            'terminals',
            'users',
            'branch',
            'terminal',
            'userName'
        ));
    }

    public function financial(Request $request)
    {
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);

        $sales = (float) SalesInvoice::whereDate('invoice_date', '>=', $dateFrom)
            ->whereDate('invoice_date', '<=', $dateTo)
            ->where('invoice_status', '!=', 'Cancelled')
            ->sum('net_amount');

        $purchases = (float) PurchaseInvoice::whereDate('purchase_date', '>=', $dateFrom)
            ->whereDate('purchase_date', '<=', $dateTo)
            ->where('invoice_status', '!=', 'Cancelled')
            ->sum('net_amount');

        $expenses = (float) Expense::whereDate('expense_date', '>=', $dateFrom)
            ->whereDate('expense_date', '<=', $dateTo)
            ->sum('amount');

        $salaryQuery = EmployeePayment::whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo);
        if (Schema::hasColumn('employee_payments', 'amount')) {
            $salaries = (float) $salaryQuery
                ->selectRaw('COALESCE(SUM(CASE WHEN COALESCE(amount, 0) > 0 THEN amount ELSE COALESCE(net_salary, 0) END), 0) as total')
                ->value('total');
        } else {
            $salaries = (float) $salaryQuery->sum('net_salary');
        }

        $vendorPayments = (float) VendorPayment::whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo)
            ->sum('amount');

        $totalOutflow = $expenses + $salaries + $vendorPayments;
        $net = $sales - $totalOutflow;

        $summary = [
            'sales' => $sales,
            'purchases' => $purchases,
            'expenses' => $expenses,
            'salaries' => $salaries,
            'vendor_payments' => $vendorPayments,
            'total_outflow' => $totalOutflow,
            'net' => $net,
        ];

        return view('reports.financial', compact('summary', 'dateFrom', 'dateTo', 'dateFromDisplay', 'dateToDisplay'));
    }

    public function sales(Request $request)
    {
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);
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

        return view('reports.sales', compact('invoices', 'summary', 'dateFrom', 'dateTo', 'dateFromDisplay', 'dateToDisplay', 'customerType'));
    }

    public function purchases(Request $request)
    {
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);

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

        return view('reports.purchases', compact('invoices', 'summary', 'dateFrom', 'dateTo', 'dateFromDisplay', 'dateToDisplay'));
    }

    public function expenses(Request $request)
    {
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);
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

        return view('reports.expenses', compact('expenses', 'totalAmount', 'dateFrom', 'dateTo', 'dateFromDisplay', 'dateToDisplay', 'categories', 'categoryUid'));
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
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);
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

        return view('reports.branch', compact('sales', 'dateFrom', 'dateTo', 'dateFromDisplay', 'dateToDisplay', 'branches', 'branch'));
    }

    public function user(Request $request)
    {
        [$dateFrom, $dateTo, $dateFromDisplay, $dateToDisplay] = $this->reportDateRange($request);
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

        return view('reports.user', compact('sales', 'dateFrom', 'dateTo', 'dateFromDisplay', 'dateToDisplay', 'users', 'userName'));
    }

    private function reportDateRange(Request $request): array
    {
        $dateFrom = $this->parseReportDate($request->get('date_from'), now()->startOfMonth());
        $dateTo = $this->parseReportDate($request->get('date_to'), now());

        return [
            $dateFrom,
            $dateTo,
            Carbon::parse($dateFrom)->format('d-M-Y'),
            Carbon::parse($dateTo)->format('d-M-Y'),
        ];
    }
}
