@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Expense</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <div class="mb-3"><label>Expense Date *</label><input type="date" name="expense_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
            <div class="mb-3"><label>Amount *</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
            <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
            <button type="submit" class="btn btn-primary">Save Expense</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection