@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Create Sales Invoice</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('sales-invoices.store') }}">
            @csrf
            <div class="mb-3"><label>Invoice Date *</label><input type="date" name="invoice_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
            <button type="submit" class="btn btn-primary">Create Invoice</button>
            <a href="{{ route('sales-invoices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection