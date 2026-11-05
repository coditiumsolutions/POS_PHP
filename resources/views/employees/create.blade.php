@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Employee</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf
            <div class="mb-3"><label>Employee Code *</label><input type="text" name="employee_code" class="form-control" required></div>
            <div class="mb-3"><label>Employee Name *</label><input type="text" name="employee_name" class="form-control" required></div>
            <div class="mb-3"><label>Basic Salary</label><input type="number" name="basic_salary" class="form-control" step="0.01"></div>
            <button type="submit" class="btn btn-primary">Save Employee</button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection