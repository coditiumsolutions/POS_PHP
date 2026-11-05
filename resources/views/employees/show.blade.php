@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Employee Details</h2>
    <div class="card"><div class="card-body">
        <p><strong>Code:</strong> {{ $employee->employee_code }}</p>
        <p><strong>Name:</strong> {{ $employee->employee_name }}</p>
        <p><strong>Basic Salary:</strong> ₨ {{ number_format($employee->basic_salary ?? 0, 2) }}</p>
        <a href="{{ route('employees.edit', $employee->uid) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back</a>
    </div></div>
</div>
@endsection