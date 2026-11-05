<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\VendorsController;
use App\Http\Controllers\SalesInvoicesController;
use App\Http\Controllers\PurchaseInvoicesController;
use App\Http\Controllers\SalesReturnsController;
use App\Http\Controllers\InventoriesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CustomerPaymentsController;
use App\Http\Controllers\VendorPaymentsController;
use App\Http\Controllers\StockAdjustmentsController;
use App\Http\Controllers\WastageController;
use App\Http\Controllers\ProductCategoriesController;
use App\Http\Controllers\ExpenseCategoriesController;
use App\Http\Controllers\WarehousesController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\MonthlySupplyController;
use App\Http\Controllers\YogurtProductionController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\AuditLogsController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // POS Routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index']);
        Route::get('/cash', [POSController::class, 'cash'])->name('cash');
        Route::get('/search-products', [POSController::class, 'searchProducts'])->name('search-products');
        Route::post('/create-sale', [POSController::class, 'createSale'])->name('create-sale');
        Route::get('/print-invoice/{uid}', [POSController::class, 'printInvoice'])->name('print-invoice');
    });

    // Products Routes
    Route::resource('products', ProductsController::class);

    // Customers Routes
    Route::resource('customers', CustomersController::class);

    // Vendors Routes
    Route::resource('vendors', VendorsController::class);

    // Sales Invoices
    Route::resource('sales-invoices', SalesInvoicesController::class);
    Route::get('/sales-invoices/{uid}/print', [SalesInvoicesController::class, 'print'])->name('sales-invoices.print');

    // Purchase Invoices
    Route::resource('purchase-invoices', PurchaseInvoicesController::class);
    Route::get('/purchase-invoices/{uid}/print', [PurchaseInvoicesController::class, 'print'])->name('purchase-invoices.print');

    // Inventory
    Route::get('/inventories', [InventoriesController::class, 'index'])->name('inventories.index');

    // Expenses
    Route::resource('expenses', ExpensesController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/sales', [ReportsController::class, 'sales'])->name('sales');
        Route::get('/purchases', [ReportsController::class, 'purchases'])->name('purchases');
        Route::get('/expenses', [ReportsController::class, 'expenses'])->name('expenses');
        Route::get('/inventory', [ReportsController::class, 'inventory'])->name('inventory');
        Route::get('/branch', [ReportsController::class, 'branch'])->name('branch');
        Route::get('/user', [ReportsController::class, 'user'])->name('user');
    });

    // Users (Admin only)
    Route::middleware(['auth'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index');
        Route::get('/create', [UsersController::class, 'create'])->name('create');
        Route::post('/', [UsersController::class, 'store'])->name('store');
        Route::get('/{uid}/edit', [UsersController::class, 'edit'])->name('edit');
        Route::put('/{uid}', [UsersController::class, 'update'])->name('update');
        Route::delete('/{uid}', [UsersController::class, 'destroy'])->name('destroy');
    });

    // Branches (Admin only)
    Route::resource('branches', BranchesController::class);

    // Sales Returns
    Route::resource('sales-returns', SalesReturnsController::class);
    
    // Customer & Vendor Payments
    Route::resource('customer-payments', CustomerPaymentsController::class);
    Route::resource('vendor-payments', VendorPaymentsController::class);
    
    // Stock Adjustments
    Route::resource('stock-adjustments', StockAdjustmentsController::class);
    
    // Wastage
    Route::resource('wastage', WastageController::class);
    
    // Product Categories & Expense Categories
    Route::resource('product-categories', ProductCategoriesController::class);
    Route::resource('expense-categories', ExpenseCategoriesController::class);
    
    // Warehouses
    Route::resource('warehouses', WarehousesController::class);

    // Employees & Payroll
    Route::resource('employees', EmployeesController::class);
    Route::get('/employees/{uid}/payroll', [EmployeesController::class, 'payroll'])->name('employees.payroll');
    Route::post('/employees/payment', [EmployeesController::class, 'storePayment'])->name('employees.store-payment');
    
    // Monthly Supply
    Route::prefix('monthly-supply')->name('monthly-supply.')->group(function () {
        Route::get('/', [MonthlySupplyController::class, 'index'])->name('index');
        Route::get('/create', [MonthlySupplyController::class, 'create'])->name('create');
        Route::post('/', [MonthlySupplyController::class, 'store'])->name('store');
        Route::get('/{uid}/edit', [MonthlySupplyController::class, 'edit'])->name('edit');
        Route::put('/{uid}', [MonthlySupplyController::class, 'update'])->name('update');
        Route::get('/overrides', [MonthlySupplyController::class, 'overrides'])->name('overrides');
        Route::post('/overrides', [MonthlySupplyController::class, 'storeOverride'])->name('store-override');
        Route::post('/generate', [MonthlySupplyController::class, 'generateInvoices'])->name('generate');
    });
    
    // Yogurt Production
    Route::prefix('yogurt-production')->name('yogurt-production.')->group(function () {
        Route::get('/', [YogurtProductionController::class, 'index'])->name('index');
        Route::get('/create', [YogurtProductionController::class, 'create'])->name('create');
        Route::post('/', [YogurtProductionController::class, 'store'])->name('store');
        Route::post('/calculate-yield', [YogurtProductionController::class, 'calculateYield'])->name('calculate-yield');
    });
    
    // Configurations (Admin only)
    Route::prefix('configurations')->name('configurations.')->group(function () {
        Route::get('/', [ConfigurationsController::class, 'index'])->name('index');
        Route::get('/create', [ConfigurationsController::class, 'create'])->name('create');
        Route::post('/', [ConfigurationsController::class, 'store'])->name('store');
        Route::post('/update', [ConfigurationsController::class, 'update'])->name('update');
    });
    
    // Audit Logs (View only)
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogsController::class, 'index'])->name('index');
        Route::get('/{uid}', [AuditLogsController::class, 'show'])->name('show');
    });
});
