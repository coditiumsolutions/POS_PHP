@extends('layouts.app')

@section('title', 'Financial Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Financial Report</h1>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-0 small">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
        </div>
    </form>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="border rounded bg-white p-3">
                <div class="text-muted small">Sales</div>
                <div class="h4 mb-0">Rs. {{ number_format($summary['sales'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded bg-white p-3">
                <div class="text-muted small">Purchases</div>
                <div class="h4 mb-0">Rs. {{ number_format($summary['purchases'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded bg-white p-3">
                <div class="text-muted small">Expenses</div>
                <div class="h4 mb-0">Rs. {{ number_format($summary['expenses'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded bg-white p-3">
                <div class="text-muted small">Gross (Sales − Purchases)</div>
                <div class="h4 mb-0">Rs. {{ number_format($summary['gross'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded bg-white p-3">
                <div class="text-muted small">Net (Gross − Expenses)</div>
                <div class="h4 mb-0">Rs. {{ number_format($summary['net'], 2) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
