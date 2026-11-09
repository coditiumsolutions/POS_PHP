@extends('layouts.app')

@section('title', 'Record Yogurt Production')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Record Yogurt Production</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('yogurt-production.store') }}">
            @csrf
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="production_date">Production Date *</label>
                    <div class="invoice-meta-field">
                        <input id="production_date" type="text" name="production_date"
                               class="form-control form-control-sm invoice-meta-input report-date-input"
                               value="{{ old('production_date', now()->format('d-M-Y')) }}"
                               placeholder="10-Nov-2026" required autocomplete="off">
                    </div>
                    <label class="invoice-meta-title" for="milk_quantity">Milk Qty (kg) *</label>
                    <div class="invoice-meta-field">
                        <input id="milk_quantity" type="number" step="0.001" min="0" name="milk_quantity"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('milk_quantity') }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="yogurt_quantity">Yogurt Qty (kg) *</label>
                    <div class="invoice-meta-field">
                        <input id="yogurt_quantity" type="number" step="0.001" min="0" name="yogurt_quantity"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('yogurt_quantity') }}" required>
                    </div>
                    <label class="invoice-meta-title"></label>
                    <div class="invoice-meta-field"></div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Record Production</button>
                <a href="{{ route('yogurt-production.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
