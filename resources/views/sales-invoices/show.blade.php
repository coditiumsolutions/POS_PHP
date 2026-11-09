@extends('layouts.app')

@section('title', 'Sales Invoice')

@section('content')
@php
    $branchName = is_object($invoice->branch) ? ($invoice->branch->branch_name ?? '—') : ($invoice->branch ?: '—');
    $isCancelled = strcasecmp((string) $invoice->invoice_status, 'Cancelled') === 0;
@endphp
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading invoice-details-heading mb-1">Sales Invoice</h1>
            <p class="text-muted mb-0">{{ $invoice->invoice_no }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sales-invoices.print', $invoice->uid) }}" class="btn btn-outline-dark" target="_blank">Print</a>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('sales-invoices.index') }}" class="btn btn-outline-secondary">Back</a>
            @unless($isCancelled)
                <form method="POST" action="{{ route('sales-invoices.destroy', $invoice->uid) }}" class="d-inline"
                      onsubmit="return confirm('Delete/cancel invoice {{ $invoice->invoice_no }}? Stock will be restored.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                </form>
            @endunless
        </div>
    </div>

    <div class="bg-white border rounded p-3 mb-3">
        <div class="invoice-meta-grid invoice-meta-form invoice-meta-readonly">
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Invoice No</div>
                <div class="invoice-meta-field">{{ $invoice->invoice_no }}</div>
                <div class="invoice-meta-title">Date</div>
                <div class="invoice-meta-field">{{ optional($invoice->invoice_date)->format('d-M-Y H:i') }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Customer</div>
                <div class="invoice-meta-field">{{ optional($invoice->customer)->customer_name ?: 'Walk-in' }}</div>
                <div class="invoice-meta-title">Branch</div>
                <div class="invoice-meta-field">{{ $branchName }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">User</div>
                <div class="invoice-meta-field">{{ $invoice->user_name ?: (optional($invoice->user)->full_name ?: '—') }}</div>
                <div class="invoice-meta-title">Terminal</div>
                <div class="invoice-meta-field">{{ $invoice->terminal ?: '—' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Payment Method</div>
                <div class="invoice-meta-field">{{ optional($invoice->paymentMethod)->payment_method_name ?: '—' }}</div>
                <div class="invoice-meta-title">Payment Status</div>
                <div class="invoice-meta-field">{{ $invoice->payment_status ?: '—' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Invoice Status</div>
                <div class="invoice-meta-field">{{ $invoice->invoice_status ?: '—' }}</div>
                <div class="invoice-meta-title">Net Amount</div>
                <div class="invoice-meta-field fw-bold">Rs. {{ number_format((float) $invoice->net_amount, 2) }}</div>
            </div>
            @if($invoice->remarks)
                <div class="invoice-meta-row">
                    <div class="invoice-meta-title">Remarks</div>
                    <div class="invoice-meta-field" style="grid-column: 2 / -1;">{{ $invoice->remarks }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white border rounded p-3 mb-3">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Tax</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->details as $i => $line)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ optional($line->product)->product_name ?: ('#'.$line->product_uid) }}</td>
                            <td class="text-end">{{ number_format((float) $line->quantity, 3) }}</td>
                            <td class="text-end">{{ number_format((float) $line->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $line->discount_amount, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $line->tax_amount, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $line->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">No items</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-semibold">
                    <tr>
                        <td colspan="6" class="text-end">Sub Total</td>
                        <td class="text-end">Rs. {{ number_format((float) $invoice->sub_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end">Discount</td>
                        <td class="text-end">Rs. {{ number_format((float) $invoice->discount_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end">Tax</td>
                        <td class="text-end">Rs. {{ number_format((float) $invoice->tax_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end">Paid</td>
                        <td class="text-end">Rs. {{ number_format((float) $invoice->paid_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end">Balance</td>
                        <td class="text-end">Rs. {{ number_format((float) $invoice->balance_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end">Net Amount</td>
                        <td class="text-end">Rs. {{ number_format((float) $invoice->net_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
