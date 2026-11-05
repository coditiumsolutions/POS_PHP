<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $todaySales = SalesInvoice::whereDate('invoice_date', $today)
            ->sum('net_amount');

        $monthSales = SalesInvoice::whereDate('invoice_date', '>=', $monthStart)
            ->sum('net_amount');

        $todayPurchases = PurchaseInvoice::whereDate('purchase_date', $today)
            ->sum('net_amount');

        $monthPurchases = PurchaseInvoice::whereDate('purchase_date', '>=', $monthStart)
            ->sum('net_amount');

        $todayExpenses = Expense::whereDate('expense_date', $today)
            ->sum('amount');

        $monthExpenses = Expense::whereDate('expense_date', '>=', $monthStart)
            ->sum('amount');

        $totalCustomers = Customer::where('is_active', true)->count();
        $totalProducts = Product::where('is_active', true)->count();

        $recentSales = SalesInvoice::with(['customer'])
            ->orderBy('invoice_date', 'desc')
            ->limit(10)
            ->get();

        $lowStockProducts = DB::table('inventories as i')
            ->join('products as p', 'i.product_uid', '=', 'p.uid')
            ->where('p.is_active', true)
            ->whereRaw('i.quantity_in_stock <= p.minimum_stock')
            ->select('p.product_name', 'i.quantity_in_stock', 'p.minimum_stock')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'todaySales',
            'monthSales',
            'todayPurchases',
            'monthPurchases',
            'todayExpenses',
            'monthExpenses',
            'totalCustomers',
            'totalProducts',
            'recentSales',
            'lowStockProducts'
        ));
    }
}
