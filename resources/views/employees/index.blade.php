@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Employees</h2>
        <a href="{{ route('employees.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Employee</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Mobile</th>
                        <th>Basic Salary</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td><strong>{{ $employee->employee_code }}</strong></td>
                        <td>{{ $employee->employee_name }}</td>
                        <td>{{ $employee->designation }}</td>
                        <td>{{ $employee->mobile_no }}</td>
                        <td class="text-end">₨ {{ number_format($employee->basic_salary ?? 0, 2) }}</td>
                        <td><span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">{{ $employee->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <a href="{{ route('employees.show', $employee->uid) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('employees.edit', $employee->uid) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <a href="{{ route('employees.payroll', $employee->uid) }}" class="btn btn-sm btn-success"><i class="fas fa-money-bill"></i> Payroll</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No employees found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
