@extends('layouts.app')

@section('title', 'Vendor Payments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading mb-1">Vendor Payments</h1>
            <p class="text-muted mb-0">Total: <strong>Rs. {{ number_format((float) $totalAmount, 2) }}</strong></p>
        </div>
        <a href="{{ route('vendor-payments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Payment
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('vendor-payments.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label small mb-0">Vendor</label>
                    <select name="vendor_uid" class="form-select form-select-sm">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->uid }}" @selected(request('vendor_uid') == $vendor->uid)>
                                {{ $vendor->vendor_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0" for="date_from">From</label>
                    <input type="text" id="date_from" name="date_from" class="form-control form-control-sm report-date-input"
                           value="{{ $dateFromDisplay }}" placeholder="24-Nov-2026" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0" for="date_to">To</label>
                    <input type="text" id="date_to" name="date_to" class="form-control form-control-sm report-date-input"
                           value="{{ $dateToDisplay }}" placeholder="24-Nov-2026" autocomplete="off">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                    <a href="{{ route('vendor-payments.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Payment No</th>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Branch</th>
                            <th>User</th>
                            <th>Mode</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_no }}</td>
                                <td>{{ optional($payment->payment_date)->format('d-M-Y') }}</td>
                                <td>{{ optional($payment->vendor)->vendor_name ?: '—' }}</td>
                                <td>{{ optional($payment->branch)->branch_name ?: '—' }}</td>
                                <td>{{ optional($payment->user)->user_name ?: '—' }}</td>
                                <td>{{ $payment->payment_mode ?: '—' }}</td>
                                <td class="text-end">{{ number_format((float) $payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No vendor payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $payments->links() }}</div>
        </div>
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
