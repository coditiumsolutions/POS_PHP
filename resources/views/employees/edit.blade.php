@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Edit Employee</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('employees.update', $employee->uid) }}">
            @csrf
            @method('PUT')
            <div class="mb-3"><label>Employee Code *</label><input type="text" name="employee_code" class="form-control" value="{{ $employee->employee_code }}" required></div>
            <div class="mb-3"><label>Employee Name *</label><input type="text" name="employee_name" class="form-control" value="{{ $employee->employee_name }}" required></div>
            <div class="mb-3"><label>Basic Salary</label><input type="number" name="basic_salary" class="form-control" step="0.01" value="{{ $employee->basic_salary }}"></div>
            <button type="submit" class="btn btn-primary">Update Employee</button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection