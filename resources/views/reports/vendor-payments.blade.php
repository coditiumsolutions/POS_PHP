@extends('layouts.app')

@section('title', 'Vendor Payments Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Vendor Payments</h1>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-0 small">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
        </div>
    </form>

    <div class="mb-2 text-muted small">Total paid: <strong>Rs. {{ number_format($total, 2) }}</strong></div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Payment No</th>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th>Mode</th>
                    <th class="text-end">Amount</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $row)
                    <tr>
                        <td>{{ $row->payment_no }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($row->payment_date)->format('d-M-Y') }}</td>
                        <td>{{ $row->vendor_name ?: '—' }}</td>
                        <td>{{ $row->payment_mode ?: '—' }}</td>
                        <td class="text-end">{{ number_format((float) $row->amount, 2) }}</td>
                        <td>{{ $row->remarks ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No vendor payments found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
