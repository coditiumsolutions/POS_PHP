@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Monthly Supply Template</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('monthly-supply.store') }}">
            @csrf
            <div class="mb-3"><label>Day of Month *</label><input type="number" name="day_of_month" class="form-control" min="1" max="31" required></div>
            <div class="mb-3"><label>Default Quantity *</label><input type="number" name="default_quantity" class="form-control" step="0.001" required></div>
            <button type="submit" class="btn btn-primary">Save Template</button>
            <a href="{{ route('monthly-supply.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection