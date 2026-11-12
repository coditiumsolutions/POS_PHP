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
        <div class="col-md-6 col-xl-3">
            <div class="border rounded p-3 h-100" style="background:#e8f1ff;">
                <div class="text-muted small">Sale</div>
                <div class="h4 mb-1">Rs. {{ number_format($summary['sales'], 2) }}</div>
                <div class="small"><a href="{{ route('reports.sales', request()->query()) }}">View sales report</a></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="border rounded p-3 h-100" style="background:#fff6e8;">
                <div class="text-muted small">Expense</div>
                <div class="h4 mb-1">Rs. {{ number_format($summary['expenses'], 2) }}</div>
                <div class="small"><a href="{{ route('reports.expenses', request()->query()) }}">View expenses report</a></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="border rounded p-3 h-100" style="background:#f3e8ff;">
                <div class="text-muted small">Salaries</div>
                <div class="h4 mb-1">Rs. {{ number_format($summary['salaries'], 2) }}</div>
                <div class="small text-muted">Employee salary payments</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="border rounded p-3 h-100" style="background:#e7f7f5;">
                <div class="text-muted small">Vendor Payments</div>
                <div class="h4 mb-1">Rs. {{ number_format($summary['vendor_payments'], 2) }}</div>
                <div class="small"><a href="{{ route('reports.vendor-payments', request()->query()) }}">View vendor payments</a></div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded mb-3">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Particular</th>
                    <th class="text-end">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sale</td>
                    <td class="text-end fw-semibold">{{ number_format($summary['sales'], 2) }}</td>
                </tr>
                <tr>
                    <td>Expense</td>
                    <td class="text-end">{{ number_format($summary['expenses'], 2) }}</td>
                </tr>
                <tr>
                    <td>Salaries</td>
                    <td class="text-end">{{ number_format($summary['salaries'], 2) }}</td>
                </tr>
                <tr>
                    <td>Vendor Payments</td>
                    <td class="text-end">{{ number_format($summary['vendor_payments'], 2) }}</td>
                </tr>
                <tr class="table-light">
                    <td class="fw-semibold">Total Outflow (Expense + Salaries + Vendor Payments)</td>
                    <td class="text-end fw-semibold">{{ number_format($summary['total_outflow'], 2) }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Purchases (invoices, reference)</td>
                    <td class="text-end text-muted">{{ number_format($summary['purchases'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="border rounded p-3" style="background:{{ $summary['net'] >= 0 ? '#eaf8ef' : '#fde8e8' }};">
        <div class="text-muted small">Net (Sale − Expense − Salaries − Vendor Payments)</div>
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
