@extends('layouts.app')

@section('title', 'Terminal Report')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading mb-3">Terminal Report</h1>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-2">
            <label class="form-label mb-0 small" for="date_from">From</label>
            <input type="text" id="date_from" name="date_from" class="form-control form-control-sm report-date-input"
                   value="{{ $dateFromDisplay }}" placeholder="24-Nov-2026" autocomplete="off">
        </div>
        <div class="col-md-2">
            <label class="form-label mb-0 small" for="date_to">To</label>
            <input type="text" id="date_to" name="date_to" class="form-control form-control-sm report-date-input"
                   value="{{ $dateToDisplay }}" placeholder="24-Nov-2026" autocomplete="off">
        </div>
        <div class="col-md-2">
            <label class="form-label mb-0 small" for="branch">Branch</label>
            <select id="branch" name="branch" class="form-select form-select-sm">
                <option value="">All Branches</option>
                @foreach($branches as $branchName)
                    <option value="{{ $branchName }}" @selected((string) $branch === (string) $branchName)>{{ $branchName }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-0 small" for="terminal">Terminal</label>
            <select id="terminal" name="terminal" class="form-select form-select-sm">
                <option value="">All Terminals</option>
                @foreach($terminals as $terminalName)
                    <option value="{{ $terminalName }}" @selected((string) $terminal === (string) $terminalName)>{{ $terminalName }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-0 small" for="user_name">User</label>
            <select id="user_name" name="user_name" class="form-select form-select-sm">
                <option value="">All Users</option>
                @foreach($users as $name)
                    <option value="{{ $name }}" @selected((string) $userName === (string) $name)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
            <a href="{{ route('reports.terminal') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
        </div>
    </form>

    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <div class="border rounded p-3" style="background:#e8f1ff;">
                <div class="text-muted small">Invoices</div>
                <strong class="fs-5">{{ $summary['invoices'] }}</strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3" style="background:#eaf8ef;">
                <div class="text-muted small">Sales</div>
                <strong class="fs-5">Rs. {{ number_format($summary['sales'], 2) }}</strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3" style="background:#fff6e8;">
                <div class="text-muted small">Paid</div>
                <strong class="fs-5">Rs. {{ number_format($summary['paid'], 2) }}</strong>
            </div>
        </div>
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Branch</th>
                    <th>Terminal</th>
                    <th>User</th>
                    <th class="text-end">Invoices</th>
                    <th class="text-end">Sales</th>
                    <th class="text-end">Paid</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ $row->branch ?: '—' }}</td>
                        <td>{{ $row->terminal ?: '—' }}</td>
                        <td>{{ $row->user_name ?: '—' }}</td>
                        <td class="text-end">{{ $row->invoice_count }}</td>
                        <td class="text-end">{{ number_format((float) $row->total_amount, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $row->total_paid, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No sales found</td></tr>
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
    flatpickr('.report-date-input', { dateFormat: 'd-M-Y', allowInput: true, disableMobile: true });
});
</script>
@endpush
