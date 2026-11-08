@extends('layouts.app')

@section('title', 'Terminal Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Terminal Report</h1>

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

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Terminal</th>
                    <th class="text-end">Invoices</th>
                    <th class="text-end">Sales</th>
                    <th class="text-end">Paid</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ $row->terminal ?: '—' }}</td>
                        <td class="text-end">{{ $row->invoice_count }}</td>
                        <td class="text-end">{{ number_format((float) $row->total_amount, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $row->total_paid, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No terminal sales found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
