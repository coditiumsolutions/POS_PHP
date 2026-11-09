@extends('layouts.app')

@section('title', 'Daily Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Daily Report</h1>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-0 small" for="date">Date</label>
            <input type="date" id="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
        </div>
        <div class="col-md-3 col-lg-2">
            <label class="form-label mb-0 small" for="pos_type">POS Type</label>
            <select id="pos_type" name="pos_type" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($allowedPosTypes as $type)
                    <option value="{{ $type }}" @selected(($posType ?? '') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
            <a href="{{ route('reports.daily', ['date' => $date]) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
        </div>
    </form>

    <div class="row g-2 mb-3">
        <div class="col-md-3"><div class="border rounded bg-white p-2">Invoices<br><strong>{{ $summary['invoices'] }}</strong></div></div>
        <div class="col-md-3"><div class="border rounded bg-white p-2">Sales<br><strong>Rs. {{ number_format($summary['sales'], 2) }}</strong></div></div>
        <div class="col-md-3"><div class="border rounded bg-white p-2">Paid<br><strong>Rs. {{ number_format($summary['paid'], 2) }}</strong></div></div>
        <div class="col-md-3"><div class="border rounded bg-white p-2">Balance<br><strong>Rs. {{ number_format($summary['balance'], 2) }}</strong></div></div>
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Time</th>
                    <th>POS Type</th>
                    <th>User</th>
                    <th>Terminal</th>
                    <th>Payment</th>
                    <th class="text-end">Net</th>
                    <th class="text-end">Paid</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td>{{ $inv->invoice_no }}</td>
                        <td>{{ optional($inv->invoice_date)->format('H:i') }}</td>
                        <td>{{ $inv->pos_type ?: 'Walk-In' }}</td>
                        <td>{{ $inv->user_name ?: '—' }}</td>
                        <td>{{ $inv->terminal ?: '—' }}</td>
                        <td>{{ optional($inv->paymentMethod)->payment_method_name ?: '—' }}</td>
                        <td class="text-end">{{ number_format((float) $inv->net_amount, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $inv->paid_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No sales for this date</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
