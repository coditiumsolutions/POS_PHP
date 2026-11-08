@extends('layouts.app')

@section('title', 'Sales Item Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Sales Item</h1>

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

    <div class="mb-2 text-muted small">
        Lines: {{ $summary['lines'] }} |
        Qty: {{ number_format($summary['qty'], 3) }} |
        Amount: Rs. {{ number_format($summary['amount'], 2) }}
        @if($branch !== '')
            | Branch: <strong>{{ $branch }}</strong>
        @endif
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover mb-0">
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
                    <tr>
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
        </table>
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
});
</script>
@endpush
