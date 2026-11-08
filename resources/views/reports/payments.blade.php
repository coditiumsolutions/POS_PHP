@extends('layouts.app')

@section('title', 'Payment Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Payment Report</h1>

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

    <div class="row g-2 mb-3">
        @forelse($byMethod as $method => $stats)
            <div class="col-md-3">
                <div class="border rounded bg-white p-2">
                    {{ $method }}<br>
                    <strong>Rs. {{ number_format($stats['amount'], 2) }}</strong>
                    <div class="small text-muted">{{ $stats['count'] }} payments</div>
                </div>
            </div>
        @empty
            <div class="col-12 text-muted">No payments in range</div>
        @endforelse
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Method</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Net</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ $row->invoice_no }}</td>
                        <td>{{ optional($row->invoice_date)->format('d-M-Y') }}</td>
                        <td>{{ $row->branch ?: '—' }}</td>
                        <td>{{ optional($row->paymentMethod)->payment_method_name ?: '—' }}</td>
                        <td class="text-end">{{ number_format((float) $row->paid_amount, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $row->net_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No payments found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
