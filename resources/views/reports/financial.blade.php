@extends('layouts.app')

@section('title', 'Financial Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Financial Report</h1>

    <div class="bg-white border rounded p-3 mb-3">
        <form method="GET">
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="date_from">From</label>
                    <div class="invoice-meta-field">
                        <input type="text" id="date_from" name="date_from"
                               class="form-control form-control-sm invoice-meta-input report-date-input"
                               value="{{ $dateFromDisplay }}" placeholder="24-Nov-2026" autocomplete="off">
                    </div>
                    <label class="invoice-meta-title" for="date_to">To</label>
                    <div class="invoice-meta-field d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" id="date_to" name="date_to"
                               class="form-control form-control-sm invoice-meta-input report-date-input"
                               value="{{ $dateToDisplay }}" placeholder="24-Nov-2026" autocomplete="off"
                               style="max-width: 11rem;">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('reports.financial') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="border rounded p-3 h-100" style="background:#e8f1ff;">
                <h2 class="h6 mb-3">Sales</h2>
                <div class="border rounded bg-white p-3 mb-2">
                    <div class="text-muted small">Sales</div>
                    <div class="h4 mb-0">Rs. {{ number_format($summary['sales'], 2) }}</div>
                </div>
                <div class="border rounded bg-white p-3 mb-2">
                    <div class="text-muted small">Purchases</div>
                    <div class="h4 mb-0">Rs. {{ number_format($summary['purchases'], 2) }}</div>
                </div>
                <div class="border rounded bg-white p-3">
                    <div class="text-muted small">Gross (Sales − Purchases)</div>
                    <div class="h4 mb-0">Rs. {{ number_format($summary['gross'], 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="border rounded p-3 h-100" style="background:#fff6e8;">
                <h2 class="h6 mb-3">Expenses</h2>
                <div class="border rounded bg-white p-3 mb-2">
                    <div class="text-muted small">Expenses</div>
                    <div class="h4 mb-0">Rs. {{ number_format($summary['expenses'], 2) }}</div>
                </div>
                <div class="border rounded bg-white p-3 mb-2">
                    <div class="text-muted small">Employee Salaries</div>
                    <div class="h4 mb-0">Rs. {{ number_format($summary['salaries'], 2) }}</div>
                </div>
                <div class="border rounded bg-white p-3">
                    <div class="text-muted small">Total Outflow (Expenses + Salaries)</div>
                    <div class="h4 mb-0">Rs. {{ number_format($summary['total_outflow'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="border rounded p-3" style="background:#eaf8ef;">
        <div class="text-muted small">Net (Gross − Total Outflow)</div>
        <div class="h3 mb-0">Rs. {{ number_format($summary['net'], 2) }}</div>
    </div>
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
});
</script>
@endpush
