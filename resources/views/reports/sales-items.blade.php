@extends('layouts.app')

@section('title', 'Sales Item Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Sales Item</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="row g-2 align-items-end mb-3" id="salesItemsFilterForm">
        <div class="col-md-3 col-lg-2">
            <label class="form-label mb-0 small" for="branch">Branch</label>
            <select id="branch" name="branch" class="form-select form-select-sm">
                <option value="">All Branches</option>
                @foreach($branches as $branchName)
                    <option value="{{ $branchName }}" @selected((string) $branch === (string) $branchName)>
                        {{ $branchName }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <label class="form-label mb-0 small" for="date_from">From</label>
            <input type="text"
                   id="date_from"
                   name="date_from"
                   class="form-control form-control-sm report-date-input"
                   value="{{ $dateFromDisplay }}"
                   placeholder="10-Nov-2026"
                   autocomplete="off">
        </div>
        <div class="col-md-3 col-lg-2">
            <label class="form-label mb-0 small" for="date_to">To</label>
            <input type="text"
                   id="date_to"
                   name="date_to"
                   class="form-control form-control-sm report-date-input"
                   value="{{ $dateToDisplay }}"
                   placeholder="10-Nov-2026"
                   autocomplete="off">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
            <a href="{{ route('reports.sales-items') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
        </div>
    </form>

    <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
        <div class="text-muted small">
            Lines: {{ $summary['lines'] }} |
            Qty: {{ number_format($summary['qty'], 3) }}
            @if($branch !== '')
                | Branch: <strong>{{ $branch }}</strong>
            @endif
            <span class="ms-2">Double-click a row to open the invoice</span>
        </div>
        <div class="ms-auto border rounded bg-white px-3 py-2">
            <span class="text-muted small me-2">Total</span>
            <strong class="fs-5">Rs. {{ number_format($summary['amount'], 2) }}</strong>
        </div>
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover mb-0" id="salesItemsTable">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                    <tr class="sales-item-row"
                        style="cursor: pointer;"
                        data-invoice-url="{{ route('sales-invoices.show', $row->sales_invoice_uid) }}">
                        <td>{{ $row->invoice_no }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($row->invoice_date)->format('d-M-Y') }}</td>
                        <td>{{ $row->branch ?: '—' }}</td>
                        <td>{{ $row->product_name ?: ($row->product_code ?: '—') }}</td>
                        <td class="text-end">{{ number_format((float) $row->quantity, 3) }}</td>
                        <td class="text-end">{{ number_format((float) $row->unit_price, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $row->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No sales items found</td></tr>
                @endforelse
            </tbody>
            @if($items->total() > 0)
                <tfoot>
                    <tr class="table-light fw-semibold">
                        <td colspan="4" class="text-end">Grand Total (all pages)</td>
                        <td class="text-end">{{ number_format($summary['qty'], 3) }}</td>
                        <td></td>
                        <td class="text-end">Rs. {{ number_format($summary['amount'], 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
        <div class="text-muted small">
            Showing {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} of {{ $items->total() }}
        </div>
        <div>{{ $items->links() }}</div>
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
    flatpickr('.report-date-input', {
        dateFormat: 'd-M-Y',
        allowInput: true,
        disableMobile: true
    });

    document.querySelectorAll('.sales-item-row').forEach(function (row) {
        row.addEventListener('dblclick', function (e) {
            if (e.target.closest('form') || e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            const url = row.getAttribute('data-invoice-url');
            if (url) {
                window.location.href = url;
            }
        });
    });
});
</script>
@endpush
