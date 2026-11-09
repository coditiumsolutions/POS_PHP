@extends('layouts.app')

@section('title', 'Add Vendor Payment')

@section('content')
@php
    $selectedVendorUid = old('vendor_uid', optional($vendor)->uid);
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading mb-1">Add Vendor Payment</h1>
            <p class="text-muted mb-0">Select a vendor and record the payment amount.</p>
        </div>
        <div class="text-muted small">Payment No: <strong>{{ $paymentNo }}</strong></div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border rounded p-3 mb-3">
        <form method="GET" action="{{ route('vendor-payments.create') }}" class="row g-2 align-items-end" id="vendorSelectForm">
            <div class="col-md-6 col-lg-5">
                <label class="form-label small mb-0" for="vendor_uid_select">Vendor *</label>
                <select id="vendor_uid_select" name="vendor_uid" class="form-select" required>
                    <option value="">Select vendor</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->uid }}" @selected((string) $selectedVendorUid === (string) $v->uid)>
                            {{ $v->vendor_name }} ({{ $v->vendor_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Load Vendor</button>
            </div>
        </form>
    </div>

    @if($vendor)
        <div class="bg-white border rounded p-3">
            <form method="POST" action="{{ route('vendor-payments.store') }}" id="vendorPaymentForm">
                @csrf
                <input type="hidden" name="vendor_uid" value="{{ $vendor->uid }}">
                <input type="hidden" name="branch_uid" value="{{ old('branch_uid', $defaultBranchUid) }}">
                <input type="hidden" name="auto_apply" value="1">

                <div class="alert alert-light border mb-3 py-2">
                    Paying to: <strong>{{ $vendor->vendor_name }}</strong>
                    <span class="text-muted small ms-1">({{ $vendor->vendor_code }})</span>
                    @if($pendingInvoices->isNotEmpty())
                        <span class="ms-2 small">
                            Pending invoices: {{ $pendingInvoices->count() }} |
                            Balance: Rs. {{ number_format((float) $pendingInvoices->sum('balance_amount'), 2) }}
                        </span>
                    @endif
                </div>

                <div class="invoice-meta-grid invoice-meta-form">
                    <div class="invoice-meta-row">
                        <label class="invoice-meta-title" for="payment_date">Payment Date *</label>
                        <div class="invoice-meta-field">
                            <input id="payment_date" type="text" name="payment_date"
                                   class="form-control form-control-sm invoice-meta-input report-date-input"
                                   value="{{ old('payment_date', now()->format('d-M-Y')) }}"
                                   placeholder="10-Nov-2026" required autocomplete="off">
                        </div>
                        <label class="invoice-meta-title" for="amount">Amount *</label>
                        <div class="invoice-meta-field">
                            <input id="amount" type="number" step="0.01" min="0.01" name="amount"
                                   class="form-control form-control-sm invoice-meta-input"
                                   value="{{ old('amount') }}" required>
                        </div>
                    </div>

                    <div class="invoice-meta-row">
                        <label class="invoice-meta-title" for="payment_mode">Payment Mode *</label>
                        <div class="invoice-meta-field">
                            <select id="payment_mode" name="payment_mode" class="form-select form-select-sm invoice-meta-input" required>
                                <option value="">Select mode</option>
                                @forelse($paymentMethods as $method)
                                    <option value="{{ $method->payment_method_name }}"
                                            data-uid="{{ $method->uid }}"
                                            @selected(old('payment_mode') === $method->payment_method_name)>
                                        {{ $method->payment_method_name }}
                                    </option>
                                @empty
                                    <option value="Cash" @selected(old('payment_mode') === 'Cash')>Cash</option>
                                    <option value="Bank Transfer" @selected(old('payment_mode') === 'Bank Transfer')>Bank Transfer</option>
                                    <option value="Cheque" @selected(old('payment_mode') === 'Cheque')>Cheque</option>
                                @endforelse
                            </select>
                            <input type="hidden" name="payment_method_uid" id="payment_method_uid" value="{{ old('payment_method_uid') }}">
                        </div>
                        <label class="invoice-meta-title" for="reference_no">Reference No</label>
                        <div class="invoice-meta-field">
                            <input id="reference_no" type="text" name="reference_no"
                                   class="form-control form-control-sm invoice-meta-input"
                                   value="{{ old('reference_no') }}">
                        </div>
                    </div>

                    <div class="invoice-meta-row">
                        <label class="invoice-meta-title" for="remarks">Remarks</label>
                        <div class="invoice-meta-field" style="grid-column: 2 / -1;">
                            <input id="remarks" type="text" name="remarks"
                                   class="form-control form-control-sm invoice-meta-input"
                                   value="{{ old('remarks') }}">
                        </div>
                    </div>
                </div>

                @if($pendingInvoices->isNotEmpty())
                    <div class="mt-3">
                        <h2 class="h6 mb-2">Apply to pending purchase invoices (optional)</h2>
                        <p class="text-muted small mb-2">
                            Leave amounts blank to auto-apply this payment to the oldest unpaid invoices.
                        </p>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th class="text-end">Net</th>
                                        <th class="text-end">Balance</th>
                                        <th class="text-end" style="width:140px">Apply Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingInvoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->purchase_invoice_no }}</td>
                                            <td>{{ optional($invoice->purchase_date)->format('d-M-Y') }}</td>
                                            <td class="text-end">{{ number_format((float) $invoice->net_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format((float) $invoice->balance_amount, 2) }}</td>
                                            <td>
                                                <input type="number"
                                                       step="0.01"
                                                       min="0"
                                                       max="{{ (float) $invoice->balance_amount }}"
                                                       name="apply_to_invoices[{{ $invoice->uid }}]"
                                                       class="form-control form-control-sm text-end apply-amount"
                                                       value="{{ old('apply_to_invoices.'.$invoice->uid) }}"
                                                       placeholder="0.00">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mt-3 mb-0">
                        No pending purchase invoices for this vendor. Payment will still be saved.
                    </div>
                @endif

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                    <a href="{{ route('vendor-payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    @else
        <div class="alert alert-light border">
            Select a vendor above to enter payment details.
        </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr('.report-date-input', {
        dateFormat: 'd-M-Y',
        allowInput: true,
        disableMobile: true
    });

    var vendorSelect = document.getElementById('vendor_uid_select');
    if (vendorSelect) {
        vendorSelect.addEventListener('change', function () {
            if (this.value) {
                document.getElementById('vendorSelectForm').submit();
            }
        });
    }

    var modeSelect = document.getElementById('payment_mode');
    var methodUid = document.getElementById('payment_method_uid');
    if (modeSelect && methodUid) {
        function syncMethodUid() {
            var opt = modeSelect.options[modeSelect.selectedIndex];
            methodUid.value = opt && opt.dataset.uid ? opt.dataset.uid : '';
        }
        modeSelect.addEventListener('change', syncMethodUid);
        syncMethodUid();
    }
});
</script>
@endpush
