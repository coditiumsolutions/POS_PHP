@extends('layouts.app')

@section('title', 'Create Sales Invoice')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Create Sales Invoice</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('sales-invoices.store') }}">
            @csrf
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="invoice_date">Invoice Date *</label>
                    <div class="invoice-meta-field">
                        <input id="invoice_date" type="text" name="invoice_date"
                               class="form-control form-control-sm invoice-meta-input report-date-input"
                               value="{{ old('invoice_date', now()->format('d-M-Y')) }}"
                               placeholder="10-Nov-2026" required autocomplete="off">
                    </div>
                    <label class="invoice-meta-title"></label>
                    <div class="invoice-meta-field text-muted small">Use Cash POS for full item entry.</div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Invoice</button>
                <a href="{{ route('sales-invoices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <a href="{{ route('pos.cash') }}" class="btn btn-outline-primary">Open Cash POS</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>document.addEventListener('DOMContentLoaded', function () { flatpickr('.report-date-input', { dateFormat: 'd-M-Y', allowInput: true, disableMobile: true }); });</script>
@endpush
