@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Reports</h1>
    <div class="row g-3">
        @foreach([
            ['route' => 'reports.sales-items', 'icon' => 'bi-basket', 'title' => 'Sales Item', 'text' => 'Sold items by invoice and product.'],
            ['route' => 'reports.daily', 'icon' => 'bi-calendar-day', 'title' => 'Daily Report', 'text' => 'Day-wise sales and collections.'],
            ['route' => 'reports.payments', 'icon' => 'bi-credit-card', 'title' => 'Payment Report', 'text' => 'Payments by method and invoice.'],
            ['route' => 'reports.vendor-payments', 'icon' => 'bi-wallet2', 'title' => 'Vendor Payments', 'text' => 'Payments made to vendors.'],
            ['route' => 'reports.terminal', 'icon' => 'bi-display', 'title' => 'Terminal Report', 'text' => 'Sales grouped by terminal.'],
            ['route' => 'reports.financial', 'icon' => 'bi-cash-stack', 'title' => 'Financial Report', 'text' => 'Sales, purchases, expenses summary.'],
        ] as $card)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi {{ $card['icon'] }}"></i> {{ $card['title'] }}</h5>
                        <p class="card-text">{{ $card['text'] }}</p>
                        <a href="{{ route($card['route']) }}" class="btn btn-primary">Open</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
