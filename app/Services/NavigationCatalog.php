<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class NavigationCatalog
{
    public const SessionKey = 'CurrentTopNav';

    public static function link(string $title, ?string $controller = null, string $action = 'index', array $extra = []): array
    {
        $route = $extra['route'] ?? null;
        if ($route && !Route::has($route)) {
            $route = null;
        }

        return array_merge([
            'title' => $title,
            'controller' => $controller,
            'action' => $action,
            'icon' => $extra['icon'] ?? 'bi-circle',
            'route' => $route,
            'children' => $extra['children'] ?? [],
        ], $extra);
    }

    public static function dashboardArea(): array
    {
        $links = [
            self::link('Bar Graphs', 'Dashboard', 'index', ['icon' => 'bi-bar-chart-fill', 'route' => 'dashboard']),
        ];
        if (Route::has('dashboard.pie')) {
            $links[] = self::link('Pie Graphs', 'Dashboard', 'pieGraphs', ['icon' => 'bi-pie-chart-fill', 'route' => 'dashboard.pie']);
        }
        if (Route::has('stock-movement.index')) {
            $links[] = self::link('Slow / Dead Stock', 'StockMovement', 'index', ['icon' => 'bi-hourglass-split', 'route' => 'stock-movement.index']);
        }

        return ['key' => 'Dashboard', 'title' => 'Dashboard', 'links' => $links];
    }

    public static function topAreas(): array
    {
        return [
            [
                'key' => 'POS',
                'title' => 'POS',
                'links' => array_values(array_filter([
                    self::link('Cash POS', 'POS', 'cash', ['icon' => 'bi-cash-stack', 'route' => 'pos.cash']),
                    Route::has('pos.barcode') ? self::link('Barcode', 'POS', 'barcode', ['icon' => 'bi-upc-scan', 'route' => 'pos.barcode']) : null,
                    Route::has('pos.credit') ? self::link('Credit POS', 'POS', 'credit', ['icon' => 'bi-calendar-month', 'route' => 'pos.credit']) : null,
                    self::link('Monthly Supply', 'MonthlySupply', 'index', ['icon' => 'bi-calendar2-check', 'route' => 'monthly-supply.index']),
                ])),
            ],
            [
                'key' => 'Customers',
                'title' => 'Customers',
                'links' => [
                    self::link('Add Customers', 'Customers', 'create', ['icon' => 'bi-person-plus', 'route' => 'customers.create']),
                    self::link('All Customers', 'Customers', 'index', ['icon' => 'bi-people', 'route' => 'customers.index']),
                    self::link('Add Payment', 'CustomerPayments', 'create', ['icon' => 'bi-cash-coin', 'route' => 'customer-payments.create']),
                    self::link('All Payments', 'CustomerPayments', 'index', ['icon' => 'bi-wallet2', 'route' => 'customer-payments.index']),
                ],
            ],
            [
                'key' => 'Employees',
                'title' => 'Employees',
                'links' => [
                    self::link('All Employees', 'Employees', 'index', ['icon' => 'bi-people', 'route' => 'employees.index']),
                    self::link('Add Employee', 'Employees', 'create', ['icon' => 'bi-person-plus', 'route' => 'employees.create']),
                    self::link('Salary Payments', 'Employees', 'salaryPayments', ['icon' => 'bi-cash-coin', 'route' => 'employees.salary-payments']),
                ],
            ],
            [
                'key' => 'Adjustments',
                'title' => 'Adjustments',
                'links' => [
                    self::link('Item Return', 'SalesReturns', 'index', ['icon' => 'bi-arrow-return-left', 'route' => 'sales-returns.index']),
                    self::link('Wastage', 'Wastage', 'index', ['icon' => 'bi-trash', 'route' => 'wastage.index']),
                    self::link('Stock Adjustment', 'StockAdjustments', 'index', ['icon' => 'bi-arrow-left-right', 'route' => 'stock-adjustments.index']),
                ],
            ],
            [
                'key' => 'AllModules',
                'title' => 'All Modules',
                'links' => [
                    self::link('Vendors', 'Vendors', 'index', ['icon' => 'bi-truck', 'route' => 'vendors.index']),
                    self::link('Products', 'Products', 'index', ['icon' => 'bi-box-seam', 'route' => 'products.index']),
                    self::link('Inventories', 'Inventories', 'index', ['icon' => 'bi-boxes', 'route' => 'inventories.index']),
                    self::link('Purchases', 'PurchaseInvoices', 'index', ['icon' => 'bi-cart-plus', 'route' => 'purchase-invoices.index']),
                    self::link('Sales', 'SalesInvoices', 'index', ['icon' => 'bi-receipt', 'route' => 'sales-invoices.index']),
                    self::link('Expenses', 'Expenses', 'index', ['icon' => 'bi-credit-card', 'route' => 'expenses.index']),
                    self::link('Employees', 'Employees', 'index', ['icon' => 'bi-person-badge', 'route' => 'employees.index']),
                    self::link('Reports', 'Reports', 'index', ['icon' => 'bi-graph-up', 'route' => 'reports.index']),
                ],
            ],
        ];
    }

    public static function nestedModuleAreas(): array
    {
        return [
            'Vendors' => [
                'key' => 'Vendors',
                'title' => 'Vendors',
                'links' => [
                    self::link('Add Vendor', 'Vendors', 'create', ['icon' => 'bi-plus-circle', 'route' => 'vendors.create']),
                    self::link('All Vendor', 'Vendors', 'index', ['icon' => 'bi-truck', 'route' => 'vendors.index']),
                ],
            ],
            'Products' => [
                'key' => 'Products',
                'title' => 'Products',
                'links' => [
                    self::link('All Categories', 'ProductCategories', 'index', [
                        'icon' => 'bi-tags',
                        'route' => 'product-categories.index',
                    ]),
                    self::link('All Products', 'Products', 'index', [
                        'icon' => 'bi-box-seam',
                        'route' => 'products.index',
                    ]),
                ],
            ],
            'Purchases' => [
                'key' => 'Purchases',
                'title' => 'Purchases',
                'links' => [
                    self::link('All Purchases', 'PurchaseInvoices', 'index', ['icon' => 'bi-cart-plus', 'route' => 'purchase-invoices.index']),
                    self::link('Add Invoice', 'PurchaseInvoices', 'create', ['icon' => 'bi-plus-circle', 'route' => 'purchase-invoices.create']),
                    self::link('Yogurt Production', 'YogurtProduction', 'index', ['icon' => 'bi-cup-straw', 'route' => 'yogurt-production.index']),
                ],
            ],
            'SalesInvoice' => [
                'key' => 'SalesInvoice',
                'title' => 'Sales Invoice',
                'links' => [
                    self::link('All Sales', 'SalesInvoices', 'index', ['icon' => 'bi-receipt', 'route' => 'sales-invoices.index']),
                ],
            ],
            'Reports' => [
                'key' => 'Reports',
                'title' => 'Reports',
                'links' => [
                    self::link('Sales Item', 'Reports', 'salesItems', ['icon' => 'bi-basket', 'route' => 'reports.sales-items']),
                    self::link('Daily Report', 'Reports', 'daily', ['icon' => 'bi-calendar-day', 'route' => 'reports.daily']),
                    self::link('Payment Report', 'Reports', 'payments', ['icon' => 'bi-credit-card', 'route' => 'reports.payments']),
                    self::link('Vendor Payments', 'Reports', 'vendorPayments', ['icon' => 'bi-wallet2', 'route' => 'reports.vendor-payments']),
                    self::link('Terminal Report', 'Reports', 'terminal', ['icon' => 'bi-display', 'route' => 'reports.terminal']),
                    self::link('Financial Report', 'Reports', 'financial', ['icon' => 'bi-cash-stack', 'route' => 'reports.financial']),
                ],
            ],
            'Employees' => [
                'key' => 'Employees',
                'title' => 'Employees',
                'links' => [
                    self::link('All Employees', 'Employees', 'index', ['icon' => 'bi-people', 'route' => 'employees.index']),
                    self::link('Add Employee', 'Employees', 'create', ['icon' => 'bi-person-plus', 'route' => 'employees.create']),
                    self::link('Salary Payments', 'Employees', 'salaryPayments', ['icon' => 'bi-cash-coin', 'route' => 'employees.salary-payments']),
                ],
            ],
            'Inventories' => [
                'key' => 'Inventories',
                'title' => 'Inventories',
                'links' => [
                    self::link('Inventory', 'Inventories', 'index', ['icon' => 'bi-boxes', 'route' => 'inventories.index']),
                    self::link('Stock Adjustment', 'StockAdjustments', 'index', ['icon' => 'bi-arrow-left-right', 'route' => 'stock-adjustments.index']),
                ],
            ],
            'Expenses' => [
                'key' => 'Expenses',
                'title' => 'Expenses',
                'links' => [
                    self::link('Expenses', 'Expenses', 'index', ['icon' => 'bi-credit-card', 'route' => 'expenses.index']),
                    self::link('Expense Categories', 'ExpenseCategories', 'index', ['icon' => 'bi-folder2', 'route' => 'expense-categories.index']),
                ],
            ],
        ];
    }

    public static function moduleSections(): array
    {
        return [
            ['title' => 'Products', 'subtitle' => 'Catalog, categories, and units', 'modules' => [
                ['name' => 'Products', 'icon' => 'bi-box-seam', 'route' => 'products.index', 'controller' => 'Products'],
                ['name' => 'Product Categories', 'icon' => 'bi-tags', 'route' => 'product-categories.index', 'controller' => 'ProductCategories'],
            ]],
            ['title' => 'Inventory', 'subtitle' => 'Stock levels and adjustments', 'modules' => [
                ['name' => 'Inventory', 'icon' => 'bi-boxes', 'route' => 'inventories.index', 'controller' => 'Inventories'],
                ['name' => 'Adjustments', 'icon' => 'bi-arrow-return-left', 'route' => 'sales-returns.index', 'controller' => 'SalesReturns'],
            ]],
            ['title' => 'Sales & Purchases', 'subtitle' => 'Trading partners, invoices, and payments', 'modules' => [
                ['name' => 'Vendors', 'icon' => 'bi-truck', 'route' => 'vendors.index', 'controller' => 'Vendors'],
                ['name' => 'Customers', 'icon' => 'bi-people', 'route' => 'customers.index', 'controller' => 'Customers'],
                ['name' => 'Purchases', 'icon' => 'bi-cart-plus', 'route' => 'purchase-invoices.index', 'controller' => 'PurchaseInvoices'],
                ['name' => 'Sales', 'icon' => 'bi-receipt', 'route' => 'sales-invoices.index', 'controller' => 'SalesInvoices'],
                ['name' => 'Customer Payments', 'icon' => 'bi-cash-coin', 'route' => 'customer-payments.index', 'controller' => 'CustomerPayments'],
                ['name' => 'Vendor Payments', 'icon' => 'bi-wallet2', 'route' => 'vendor-payments.index', 'controller' => 'VendorPayments'],
            ]],
            ['title' => 'Expenses', 'subtitle' => 'Business expense tracking', 'modules' => [
                ['name' => 'Expense Categories', 'icon' => 'bi-folder2', 'route' => 'expense-categories.index', 'controller' => 'ExpenseCategories'],
                ['name' => 'Expenses', 'icon' => 'bi-credit-card', 'route' => 'expenses.index', 'controller' => 'Expenses'],
            ]],
            ['title' => 'HR & Payroll', 'subtitle' => 'Employees, salary, allowances, and payments', 'modules' => [
                ['name' => 'Employees', 'icon' => 'bi-person-badge', 'route' => 'employees.index', 'controller' => 'Employees'],
            ]],
            ['title' => 'Organization', 'subtitle' => 'Users, branches, and warehouses', 'modules' => [
                ['name' => 'Users', 'icon' => 'bi-person-gear', 'route' => 'users.index', 'controller' => 'Users'],
                ['name' => 'Branches', 'icon' => 'bi-building', 'route' => 'branches.index', 'controller' => 'Branches'],
                ['name' => 'Warehouses', 'icon' => 'bi-house-door', 'route' => 'warehouses.index', 'controller' => 'Warehouses'],
            ]],
            ['title' => 'Reports & Logs', 'subtitle' => 'Sales reports and audit history', 'modules' => [
                ['name' => 'Reports', 'icon' => 'bi-graph-up', 'route' => 'reports.index', 'controller' => 'Reports'],
                ['name' => 'Audit Logs', 'icon' => 'bi-journal-text', 'route' => 'audit-logs.index', 'controller' => 'AuditLogs'],
            ]],
        ];
    }

    public static function setupModules(): array
    {
        return [
            ['name' => 'Users', 'icon' => 'bi-person-gear', 'route' => 'users.index', 'controller' => 'Users'],
            ['name' => 'Branches', 'icon' => 'bi-building', 'route' => 'branches.index', 'controller' => 'Branches'],
            ['name' => 'Warehouses', 'icon' => 'bi-house-door', 'route' => 'warehouses.index', 'controller' => 'Warehouses'],
            ['name' => 'Configs', 'icon' => 'bi-sliders', 'route' => 'configurations.index', 'controller' => 'Configurations'],
            ['name' => 'Audit Logs', 'icon' => 'bi-journal-text', 'route' => 'audit-logs.index', 'controller' => 'AuditLogs'],
        ];
    }

    public static function findArea(?string $key): array
    {
        if (!$key || strcasecmp($key, 'Dashboard') === 0) {
            return self::dashboardArea();
        }
        foreach (self::topAreas() as $area) {
            if (strcasecmp($area['key'], $key) === 0) {
                return $area;
            }
        }
        $nested = self::nestedModuleAreas();
        foreach ($nested as $area) {
            if (strcasecmp($area['key'], $key) === 0) {
                return $area;
            }
        }
        // Map common route prefixes to nested areas
        $map = [
            'vendors' => 'Vendors',
            'products' => 'Products',
            'product-categories' => 'Products',
            'purchase-invoices' => 'Purchases',
            'yogurt-production' => 'Purchases',
            'sales-invoices' => 'SalesInvoice',
            'reports' => 'Reports',
            'employees' => 'Employees',
            'inventories' => 'Inventories',
            'stock-adjustments' => 'Inventories',
            'expenses' => 'Expenses',
            'expense-categories' => 'Expenses',
        ];
        if (isset($map[$key]) && isset($nested[$map[$key]])) {
            return $nested[$map[$key]];
        }

        return self::dashboardArea();
    }

    public static function isUnderAllModules(?string $key): bool
    {
        if (!$key) {
            return false;
        }
        if (strcasecmp($key, 'AllModules') === 0) {
            return true;
        }
        foreach (self::nestedModuleAreas() as $area) {
            if (strcasecmp($area['key'], $key) === 0) {
                return true;
            }
        }
        return false;
    }

    public static function inferKeyFromRequest(): ?string
    {
        $name = request()->route()?->getName() ?? '';
        if ($name === 'dashboard' || $name === '' || $name === 'nav.clear') {
            return 'Dashboard';
        }
        if (str_starts_with($name, 'pos.')) {
            return 'POS';
        }
        if (str_starts_with($name, 'customers.') || str_starts_with($name, 'customer-payments.')) {
            return 'Customers';
        }
        if (str_starts_with($name, 'employees.')) {
            return 'Employees';
        }
        if (str_starts_with($name, 'sales-returns.') || str_starts_with($name, 'wastage.') || str_starts_with($name, 'stock-adjustments.')) {
            return 'Adjustments';
        }
        if (str_starts_with($name, 'vendors.')) {
            return 'Vendors';
        }
        if (str_starts_with($name, 'products.') || str_starts_with($name, 'product-categories.')) {
            return 'Products';
        }
        if (str_starts_with($name, 'purchase-invoices.') || str_starts_with($name, 'yogurt-production.')) {
            return 'Purchases';
        }
        if (str_starts_with($name, 'sales-invoices.')) {
            return 'SalesInvoice';
        }
        if (str_starts_with($name, 'reports.')) {
            return 'Reports';
        }
        if (str_starts_with($name, 'inventories.')) {
            return 'Inventories';
        }
        if (str_starts_with($name, 'expenses.') || str_starts_with($name, 'expense-categories.')) {
            return 'Expenses';
        }
        if (str_starts_with($name, 'monthly-supply.')) {
            return 'POS';
        }
        if (str_starts_with($name, 'modules.') || str_starts_with($name, 'setup.')) {
            return session(self::SessionKey) ?: 'AllModules';
        }
        return session(self::SessionKey);
    }

    /**
     * Resolve which sidebar area to show.
     * - Products / Reports / Employees pages always use their own sidebars.
     * - While All Modules is selected, keep the All Modules module list.
     */
    public static function resolveCurrentKey(): string
    {
        $sessionKey = session(self::SessionKey);
        $routeName = request()->route()?->getName() ?? '';

        if (str_starts_with($routeName, 'products.') || str_starts_with($routeName, 'product-categories.')) {
            return 'Products';
        }

        if (str_starts_with($routeName, 'reports.')) {
            return 'Reports';
        }

        if (str_starts_with($routeName, 'employees.')) {
            return 'Employees';
        }

        if ($sessionKey && strcasecmp($sessionKey, 'AllModules') === 0) {
            return 'AllModules';
        }

        return self::inferKeyFromRequest() ?: ($sessionKey ?: 'Dashboard');
    }
}
