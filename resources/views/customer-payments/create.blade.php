@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Record Customer Payment</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('customer-payments.store') }}">
            @csrf
            <div class="mb-3"><label>Payment Date *</label><input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
            <div class="mb-3"><label>Amount *</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
            <div class="mb-3"><label>Payment Mode *</label><select name="payment_mode" class="form-control" required><option>Cash</option><option>Bank Transfer</option><option>Cheque</option></select></div>
            <button type="submit" class="btn btn-primary">Record Payment</button>
            <a href="{{ route('customer-payments.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection