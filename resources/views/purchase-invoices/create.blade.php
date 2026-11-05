@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Create Purchase Invoice</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('purchase-invoices.store') }}">
            @csrf
            <div class="mb-3"><label>Purchase Date *</label><input type="date" name="purchase_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
            <button type="submit" class="btn btn-primary">Create Purchase</button>
            <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection