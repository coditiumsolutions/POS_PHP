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
