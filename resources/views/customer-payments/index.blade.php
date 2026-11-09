@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Customer Payments</h2>
        <a href="{{ route('customer-payments.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Payment</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="customer_uid" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->uid }}" {{ request('customer_uid') == $customer->uid ? 'selected' : '' }}>{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="date_from" class="form-control report-date-input"
                           value="{{ $dateFromDisplay ?? '' }}" placeholder="24-Nov-2026" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <input type="text" name="date_to" class="form-control report-date-input"
                           value="{{ $dateToDisplay ?? '' }}" placeholder="24-Nov-2026" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button>
                </div>
            </form>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Payment No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><strong>{{ $payment->payment_no }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td>{{ $payment->customer->customer_name ?? 'N/A' }}</td>
                        <td class="text-end"><strong>₨ {{ number_format($payment->amount, 2) }}</strong></td>
                        <td><span class="badge bg-info">{{ $payment->payment_mode }}</span></td>
                        <td><small>{{ $payment->reference_no }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No payments found</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th class="text-end">₨ {{ number_format($totalAmount ?? 0, 2) }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
            {{ $payments->links() }}
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
