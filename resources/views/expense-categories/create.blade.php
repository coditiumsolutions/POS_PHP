@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Expense Categories</h2>
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Form for creating Expense Categories</p>
            <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
