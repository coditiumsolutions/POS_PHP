@extends('layouts.app')

@section('title', 'Purchase Invoice')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading mb-1">Purchase Invoice</h1>
            <p class="text-muted mb-0">{{ $invoice->purchase_invoice_no }}</p>
        </div>
        <div class="d-flex gap-2">
            @if(Route::has('purchase-invoices.print'))
                <a href="{{ route('purchase-invoices.print', $invoice->uid) }}" class="btn btn-outline-dark" target="_blank">Print</a>
            @endif
            <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="bg-white border rounded p-3 mb-3">
        <div class="invoice-meta-grid invoice-meta-form invoice-meta-readonly">
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Invoice No</div>
                <div class="invoice-meta-field">{{ $invoice->purchase_invoice_no }}</div>
                <div class="invoice-meta-title">Date</div>
                <div class="invoice-meta-field">{{ optional($invoice->purchase_date)->format('d-M-Y') }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Vendor</div>
                <div class="invoice-meta-field">{{ optional($invoice->vendor)->vendor_name ?: '—' }}</div>
                <div class="invoice-meta-title">Warehouse</div>
                <div class="invoice-meta-field">{{ optional($invoice->warehouse)->warehouse_name ?: '—' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Net Amount</div>
                <div class="invoice-meta-field">{{ number_format((float) $invoice->net_amount, 2) }}</div>
                <div class="invoice-meta-title">Payment Status</div>
                <div class="invoice-meta-field">{{ $invoice->payment_status }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->details as $i => $line)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ optional($line->product)->product_name ?: ('#'.$line->product_uid) }}</td>
                            <td class="text-end">{{ number_format((float) $line->quantity, 3) }}</td>
                            <td class="text-end">{{ number_format((float) $line->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $line->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
