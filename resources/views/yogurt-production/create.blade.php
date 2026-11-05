@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Record Yogurt Production</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('yogurt-production.store') }}">
            @csrf
            <div class="mb-3"><label>Production Date *</label><input type="date" name="production_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
            <div class="mb-3"><label>Milk Quantity (kg) *</label><input type="number" name="milk_quantity" class="form-control" step="0.001" required></div>
            <div class="mb-3"><label>Yogurt Quantity (kg) *</label><input type="number" name="yogurt_quantity" class="form-control" step="0.001" required></div>
            <button type="submit" class="btn btn-primary">Record Production</button>
            <a href="{{ route('yogurt-production.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection