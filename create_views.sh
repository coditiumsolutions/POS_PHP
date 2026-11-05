#!/bin/bash
# Script to create all remaining views efficiently

cd /workspace/resources/views

# Sales Invoices Views
cat > sales-invoices/index.blade.php << 'EOF'
@extends('layouts.app')
@section('title', 'Sales Invoices')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Sales Invoices</h1>
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="customer_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="walkin" {{ request('customer_type') == 'walkin' ? 'selected' : '' }}>Walk-in</option>
                        <option value="credit" {{ request('customer_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="monthly" {{ request('customer_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_no }}</td>
                            <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                            <td>{{ $invoice->customer ? $invoice->customer->customer_name : 'Walk-in' }}</td>
                            <td>Rs. {{ number_format($invoice->net_amount, 2) }}</td>
                            <td>Rs. {{ number_format($invoice->paid_amount, 2) }}</td>
                            <td><span class="badge bg-{{ $invoice->payment_status == 'Paid' ? 'success' : 'warning' }}">{{ $invoice->payment_status }}</span></td>
                            <td>
                                <a href="{{ route('sales-invoices.show', $invoice->uid) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('sales-invoices.print', $invoice->uid) }}" class="btn btn-sm btn-secondary" target="_blank">Print</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No invoices found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $invoices->links() }}
            <div class="mt-3">
                <strong>Total Net: Rs. {{ number_format($totalNet ?? 0, 2) }}</strong> |
                <strong>Total Paid: Rs. {{ number_format($totalPaid ?? 0, 2) }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection
EOF

# Purchase Invoices Index
cat > purchase-invoices/index.blade.php << 'EOF'
@extends('layouts.app')
@section('title', 'Purchase Invoices')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Purchase Invoices</h1>
        <a href="{{ route('purchase-invoices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Purchase
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="vendor_uid" class="form-select">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $vendor)
                        <option value="{{ $vendor->uid }}" {{ request('vendor_uid') == $vendor->uid ? 'selected' : '' }}>{{ $vendor->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->purchase_invoice_no }}</td>
                            <td>{{ $invoice->purchase_date->format('Y-m-d') }}</td>
                            <td>{{ $invoice->vendor->vendor_name }}</td>
                            <td>Rs. {{ number_format($invoice->net_amount, 2) }}</td>
                            <td>Rs. {{ number_format($invoice->paid_amount, 2) }}</td>
                            <td><span class="badge bg-{{ $invoice->payment_status == 'Paid' ? 'success' : 'warning' }}">{{ $invoice->payment_status }}</span></td>
                            <td>
                                <a href="{{ route('purchase-invoices.show', $invoice->uid) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('purchase-invoices.print', $invoice->uid) }}" class="btn btn-sm btn-secondary" target="_blank">Print</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No invoices found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
EOF

# Expenses Index
cat > expenses/index.blade.php << 'EOF'
@extends('layouts.app')
@section('title', 'Expenses')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Expenses</h1>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Record Expense
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="category_uid" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->uid }}" {{ request('category_uid') == $category->uid ? 'selected' : '' }}>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Expense No</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_no }}</td>
                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td>{{ $expense->category->category_name }}</td>
                            <td>Rs. {{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->payment_mode }}</td>
                            <td>
                                <a href="{{ route('expenses.edit', $expense->uid) }}" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No expenses found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $expenses->links() }}
            <div class="mt-3">
                <strong>Total: Rs. {{ number_format($totalAmount ?? 0, 2) }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection
EOF

# Reports Index
cat > reports/index.blade.php << 'EOF'
@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Reports</h1>
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-receipt"></i> Sales Report</h5>
                    <p class="card-text">View sales by date range, customer type, and more.</p>
                    <a href="{{ route('reports.sales') }}" class="btn btn-primary">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-cart-check"></i> Purchase Report</h5>
                    <p class="card-text">View purchases by date range and vendor.</p>
                    <a href="{{ route('reports.purchases') }}" class="btn btn-primary">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-cash-coin"></i> Expense Report</h5>
                    <p class="card-text">View expenses by category and date range.</p>
                    <a href="{{ route('reports.expenses') }}" class="btn btn-primary">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-boxes"></i> Inventory Report</h5>
                    <p class="card-text">View current stock levels and values.</p>
                    <a href="{{ route('reports.inventory') }}" class="btn btn-primary">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-building"></i> Branch Report</h5>
                    <p class="card-text">View sales by branch.</p>
                    <a href="{{ route('reports.branch') }}" class="btn btn-primary">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-person"></i> User Report</h5>
                    <p class="card-text">View sales by user/cashier.</p>
                    <a href="{{ route('reports.user') }}" class="btn btn-primary">View Report</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
EOF

echo "Views created successfully!"
