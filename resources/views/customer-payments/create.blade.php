@extends('layouts.app')

@section('title', 'Add Customer Payment')

@section('content')
@php
    $selectedCustomerUid = old('customer_uid', optional($customer)->uid);
@endphp
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Customer Payment</h1>

    <div class="bg-white border rounded p-3 mb-3">
        <form method="GET" action="{{ route('customer-payments.create') }}" class="row g-2 align-items-end" id="customerSelectForm">
            <div class="col-md-6 col-lg-5">
                <label class="form-label small mb-0" for="customer_uid_select">Customer *</label>
                <select id="customer_uid_select" name="customer_uid" class="form-select" required>
                    <option value="">Select customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->uid }}" @selected((string) $selectedCustomerUid === (string) $c->uid)>
                            {{ $c->customer_name }} ({{ $c->customer_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Load Customer</button>
            </div>
        </form>
    </div>

    @if($customer)
        <div class="bg-white border rounded p-3">
            <form method="POST" action="{{ route('customer-payments.store') }}">
                @csrf
                <input type="hidden" name="customer_uid" value="{{ $customer->uid }}">

                <div class="alert alert-light border mb-3 py-2">
                    Paying from: <strong>{{ $customer->customer_name }}</strong>
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
                                @foreach(['Cash', 'Bank Transfer', 'Cheque', 'Card'] as $mode)
                                    <option value="{{ $mode }}" @selected(old('payment_mode', 'Cash') === $mode)>{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="invoice-meta-title" for="reference_no">Reference No</label>
                        <div class="invoice-meta-field">
                            <input id="reference_no" type="text" name="reference_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('reference_no') }}">
                        </div>
                    </div>
                    <div class="invoice-meta-row">
                        <label class="invoice-meta-title" for="remarks">Remarks</label>
                        <div class="invoice-meta-field" style="grid-column: 2 / -1;">
                            <input id="remarks" type="text" name="remarks" class="form-control form-control-sm invoice-meta-input" value="{{ old('remarks') }}">
                        </div>
                    </div>
                </div>

                @if($pendingInvoices->isNotEmpty())
                    <div class="mt-3">
                        <h2 class="h6 mb-2">Apply to pending invoices (optional)</h2>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th class="text-end">Balance</th>
                                        <th class="text-end" style="width:140px">Apply</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingInvoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->invoice_no }}</td>
                                            <td class="text-end">{{ number_format((float) $invoice->balance_amount, 2) }}</td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="apply_to_invoices[{{ $invoice->uid }}]"
                                                       class="form-control form-control-sm text-end" placeholder="0.00">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                    <a href="{{ route('customer-payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    @else
        <div class="alert alert-light border">Select a customer above to enter payment details.</div>
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
    flatpickr('.report-date-input', { dateFormat: 'd-M-Y', allowInput: true, disableMobile: true });
    var sel = document.getElementById('customer_uid_select');
    if (sel) sel.addEventListener('change', function () { if (this.value) document.getElementById('customerSelectForm').submit(); });
});
</script>
@endpush
