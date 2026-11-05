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
