@extends('layouts.app')

@section('title', 'Dashboard - LipSip POS')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Dashboard</h1>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Today's Sales</h5>
                    <h2 class="mb-0">Rs. {{ number_format($todaySales, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Month Sales</h5>
                    <h2 class="mb-0">Rs. {{ number_format($monthSales, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Today's Purchases</h5>
                    <h2 class="mb-0">Rs. {{ number_format($todayPurchases, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Today's Expenses</h5>
                    <h2 class="mb-0">Rs. {{ number_format($todayExpenses, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Sales</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                <tr>
                                    <td>{{ $sale->invoice_no }}</td>
                                    <td>{{ $sale->customer ? $sale->customer->customer_name : 'Walk-in' }}</td>
                                    <td>Rs. {{ number_format($sale->net_amount, 2) }}</td>
                                    <td>{{ $sale->invoice_date->format('Y-m-d H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No sales found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Low Stock Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Min Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $product)
                                <tr>
                                    <td>{{ $product->product_name }}</td>
                                    <td class="text-danger">{{ $product->quantity_in_stock }}</td>
                                    <td>{{ $product->minimum_stock }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">All products are well stocked</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
