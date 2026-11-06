<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') - Coditium POS</title>
    <meta name="theme-color" content="#0f766e">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @stack('styles')
</head>
<body>
@php
    $user = Auth::user();
    $posActive = request()->routeIs('pos.*');
    $customersActive = request()->routeIs('customers.*') || request()->routeIs('customer-payments.*');
    $employeesActive = request()->routeIs('employees.*');
    $adjustmentsActive = request()->routeIs('sales-returns.*') || request()->routeIs('wastage.*') || request()->routeIs('stock-adjustments.*');
    $allModulesActive = request()->routeIs('vendors.*')
        || request()->routeIs('products.*')
        || request()->routeIs('inventories.*')
        || request()->routeIs('purchase-invoices.*')
        || request()->routeIs('sales-invoices.*')
        || request()->routeIs('expenses.*')
        || request()->routeIs('reports.*');
@endphp

<div class="pos-header-bars no-print">
    <header class="pos-topbar-primary">
        <a class="pos-brand" href="{{ route('dashboard') }}">
            <span>Coditium POS</span>
        </a>
        <div class="pos-user">
            <div class="pos-session-meta" title="Signed-in user session">
                <span class="pos-session-item"><span class="pos-session-label">User</span> {{ $user->user_name ?? $user->full_name ?? '—' }}</span>
                <span class="pos-session-item"><span class="pos-session-label">Terminal</span> {{ $user->terminal_name ?? '—' }}</span>
                <span class="pos-session-item"><span class="pos-session-label">Branch</span> {{ $user->branch ?? '—' }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </header>

    {{-- White second navbar (ported from POS_MT pos-topbar-secondary / NavigationCatalog.TopAreas) --}}
    <header class="pos-topbar-secondary">
        <nav class="pos-top-nav">
            <a class="pos-top-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">Home</a>

            @if(Route::has('pos.cash'))
                <a class="pos-top-link {{ $posActive ? 'active' : '' }}"
                   href="{{ route('pos.cash') }}">POS</a>
            @endif

            @if(Route::has('customers.index'))
                <a class="pos-top-link {{ $customersActive ? 'active' : '' }}"
                   href="{{ route('customers.index') }}">Customers</a>
            @endif

            @if(Route::has('employees.index'))
                <a class="pos-top-link {{ $employeesActive ? 'active' : '' }}"
                   href="{{ route('employees.index') }}">Employees</a>
            @endif

            @if(Route::has('sales-returns.index'))
                <a class="pos-top-link {{ $adjustmentsActive ? 'active' : '' }}"
                   href="{{ route('sales-returns.index') }}">Adjustments</a>
            @endif

            <div class="pos-top-dropdown {{ $allModulesActive ? 'active' : '' }}" data-top-dropdown>
                <div class="pos-top-dropdown-trigger">
                    <a class="pos-top-link {{ $allModulesActive ? 'active' : '' }}"
                       href="{{ Route::has('vendors.index') ? route('vendors.index') : '#' }}">All Modules</a>
                    <button type="button"
                            class="pos-top-twistee"
                            aria-expanded="false"
                            aria-label="Expand All Modules"
                            data-top-twistee>
                        <i class="bi bi-caret-right-fill" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="pos-top-dropdown-menu" hidden>
                    @if(Route::has('vendors.index'))
                        <a class="pos-top-dropdown-item" href="{{ route('vendors.index') }}">Vendors</a>
                    @endif
                    @if(Route::has('products.index'))
                        <a class="pos-top-dropdown-item" href="{{ route('products.index') }}">Products</a>
                    @endif
                    @if(Route::has('inventories.index'))
                        <a class="pos-top-dropdown-item" href="{{ route('inventories.index') }}">Inventories</a>
                    @endif
                    @if(Route::has('purchase-invoices.index'))
                        <a class="pos-top-dropdown-item" href="{{ route('purchase-invoices.index') }}">Purchases</a>
                    @endif
                    @if(Route::has('sales-invoices.index'))
                        <a class="pos-top-dropdown-item" href="{{ route('sales-invoices.index') }}">Sales</a>
                    @endif
                    @if(Route::has('expenses.index'))
                        <a class="pos-top-dropdown-item" href="{{ route('expenses.index') }}">Expenses</a>
                    @endif
                    @if(Route::has('employees.index'))
                        <a class="pos-top-dropdown-item" href="{{ route('employees.index') }}">Employees</a>
                    @endif
                    @if(Route::has('reports.index'))
                        <a class="pos-top-dropdown-item" href="{{ route('reports.index') }}">Reports</a>
                    @elseif(Route::has('reports.sales'))
                        <a class="pos-top-dropdown-item" href="{{ route('reports.sales') }}">Reports</a>
                    @endif
                </div>
            </div>
        </nav>
    </header>

    <div class="pos-gold-bar" aria-hidden="true"></div>
</div>

{{-- Sidebar (existing module list) --}}
<nav class="sidebar no-print">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.cash') }}">
                    <i class="bi bi-cash-register"></i> POS
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('sales-invoices.*') ? 'active' : '' }}" href="{{ route('sales-invoices.index') }}">
                    <i class="bi bi-receipt"></i> Sales Invoices
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}" href="{{ route('purchase-invoices.index') }}">
                    <i class="bi bi-cart-check"></i> Purchase Invoices
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                    <i class="bi bi-people"></i> Customers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">
                    <i class="bi bi-truck"></i> Vendors
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="bi bi-box-seam"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inventories.*') ? 'active' : '' }}" href="{{ route('inventories.index') }}">
                    <i class="bi bi-boxes"></i> Inventory
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                    <i class="bi bi-cash-coin"></i> Expenses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('sales-returns.*') ? 'active' : '' }}" href="{{ route('sales-returns.index') }}">
                    <i class="bi bi-arrow-return-left"></i> Sales Returns
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer-payments.*') ? 'active' : '' }}" href="{{ route('customer-payments.index') }}">
                    <i class="bi bi-cash-stack"></i> Customer Payments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vendor-payments.*') ? 'active' : '' }}" href="{{ route('vendor-payments.index') }}">
                    <i class="bi bi-wallet"></i> Vendor Payments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}" href="{{ route('stock-adjustments.index') }}">
                    <i class="bi bi-arrow-left-right"></i> Stock Adjustments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('wastage.*') ? 'active' : '' }}" href="{{ route('wastage.index') }}">
                    <i class="bi bi-trash"></i> Wastage
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                    <i class="bi bi-person-badge"></i> Employees
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('monthly-supply.*') ? 'active' : '' }}" href="{{ route('monthly-supply.index') }}">
                    <i class="bi bi-calendar-check"></i> Monthly Supply
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('yogurt-production.*') ? 'active' : '' }}" href="{{ route('yogurt-production.index') }}">
                    <i class="bi bi-cup-straw"></i> Yogurt Production
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="bi bi-bar-chart-line"></i> Reports
                </a>
            </li>
            @if(method_exists($user, 'isAdmin') ? $user->isAdmin() : true)
            <li class="nav-item">
                <hr class="my-2">
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-person-gear"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}" href="{{ route('branches.index') }}">
                    <i class="bi bi-building"></i> Branches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}" href="{{ route('product-categories.index') }}">
                    <i class="bi bi-tags"></i> Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}" href="{{ route('warehouses.index') }}">
                    <i class="bi bi-building-gear"></i> Warehouses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('configurations.*') ? 'active' : '' }}" href="{{ route('configurations.index') }}">
                    <i class="bi bi-gear"></i> Configuration
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                    <i class="bi bi-file-text"></i> Audit Logs
                </a>
            </li>
            @endif
        </ul>
    </div>
</nav>

<main class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="{{ asset('js/site.js') }}"></script>
@stack('scripts')
</body>
</html>
