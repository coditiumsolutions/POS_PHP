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
