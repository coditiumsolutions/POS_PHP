@extends('layouts.app')
@section('title', 'Expense Report')
@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Expense Report</h1>
    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-0 small" for="date_from">From</label>
            <input type="text" id="date_from" name="date_from" class="form-control form-control-sm report-date-input"
                   value="{{ $dateFromDisplay }}" placeholder="24-Nov-2026" autocomplete="off">
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small" for="date_to">To</label>
            <input type="text" id="date_to" name="date_to" class="form-control form-control-sm report-date-input"
                   value="{{ $dateToDisplay }}" placeholder="24-Nov-2026" autocomplete="off">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
        </div>
    </form>
    <div class="text-muted small mb-2">Total: <strong>Rs. {{ number_format((float) $totalAmount, 2) }}</strong></div>
    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Expense No</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th class="text-end">Amount</th>
                    <th>Mode</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->expense_no }}</td>
                        <td>{{ optional($expense->expense_date)->format('d-M-Y') }}</td>
                        <td>{{ optional($expense->category)->category_name ?: '—' }}</td>
                        <td class="text-end">{{ number_format((float) $expense->amount, 2) }}</td>
                        <td>{{ $expense->payment_mode ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No expenses found</td></tr>
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
<script>document.addEventListener('DOMContentLoaded', function () { flatpickr('.report-date-input', { dateFormat: 'd-M-Y', allowInput: true, disableMobile: true }); });</script>
@endpush