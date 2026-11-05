<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS PHP')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-size: 14px;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 56px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background: #f8f9fa;
            width: 250px;
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 56px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: #333;
            padding: 10px 16px;
        }
        .sidebar .nav-link:hover {
            background: #e9ecef;
        }
        .sidebar .nav-link.active {
            color: #007bff;
            background: #e7f1ff;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        @media print {
            .sidebar, .navbar, .no-print {
                display: none !important;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-dark bg-dark fixed-top no-print">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-shop"></i> POS PHP
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="bi bi-person-circle"></i> {{ Auth::user()->full_name }}
                    ({{ Auth::user()->role_name }})
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
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
                @if(Auth::user()->isAdmin())
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

    <!-- Main Content -->
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
    @stack('scripts')
</body>
</html>
