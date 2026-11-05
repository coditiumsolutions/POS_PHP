#!/bin/bash
set -e
cd /workspace/resources/views

# Employees Index
cat > employees/index.blade.php << 'VIEWEOF'
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
VIEWEOF

# Monthly Supply Index
cat > monthly-supply/index.blade.php << 'VIEWEOF'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Monthly Supply Templates</h2>
        <div>
            <a href="{{ route('monthly-supply.overrides') }}" class="btn btn-secondary me-2"><i class="fas fa-calendar-alt"></i> Overrides</a>
            <a href="{{ route('monthly-supply.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Template</a>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Generate Invoices for Date</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('monthly-supply.generate') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Generation Date</label>
                    <input type="date" name="generation_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success"><i class="fas fa-play"></i> Generate Invoices</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Day of Month</th>
                        <th>Default Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplies as $supply)
                    <tr>
                        <td>{{ $supply->customer->customer_name }}</td>
                        <td>{{ $supply->product->product_name }}</td>
                        <td class="text-center"><strong>{{ $supply->day_of_month }}</strong></td>
                        <td class="text-end">{{ number_format($supply->default_quantity, 3) }}</td>
                        <td><span class="badge bg-{{ $supply->is_active ? 'success' : 'secondary' }}">{{ $supply->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <a href="{{ route('monthly-supply.edit', $supply->uid) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No monthly supply templates found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $supplies->links() }}
        </div>
    </div>
</div>
@endsection
VIEWEOF

# Yogurt Production Index
cat > yogurt-production/index.blade.php << 'VIEWEOF'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Yogurt Production</h2>
        <a href="{{ route('yogurt-production.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Record Production</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button>
                </div>
            </form>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Reference No</th>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productions as $production)
                    <tr>
                        <td><strong>{{ $production->reference_no }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($production->transaction_date)->format('d M Y') }}</td>
                        <td>{{ $production->product->product_name }}</td>
                        <td class="text-end">{{ number_format($production->quantity, 3) }}</td>
                        <td><small>{{ $production->remarks }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No production records found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $productions->links() }}
        </div>
    </div>
</div>
@endsection
VIEWEOF

# Configurations Index
cat > configurations/index.blade.php << 'VIEWEOF'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>System Configuration</h2>
        <a href="{{ route('configurations.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Setting</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('configurations.update') }}">
                @csrf
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="30%">Configuration Key</th>
                            <th width="50%">Value</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($configs as $index => $config)
                        <tr>
                            <td><strong>{{ $config->config_key }}</strong>
                                <input type="hidden" name="configs[{{ $index }}][key]" value="{{ $config->config_key }}">
                            </td>
                            <td><input type="text" name="configs[{{ $index }}][value]" class="form-control" value="{{ $config->config_value }}"></td>
                            <td><small class="text-muted">{{ $config->description }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">No configuration settings found</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($configs->count() > 0)
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save All Changes</button>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
VIEWEOF

# Audit Logs Index
cat > audit-logs/index.blade.php << 'VIEWEOF'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Audit Logs</h2>
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-2">
                    <select name="user_uid" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->uid }}" {{ request('user_uid') == $user->uid ? 'selected' : '' }}>{{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="action_type" class="form-select">
                        <option value="">All Actions</option>
                        @foreach($actionTypes as $type)
                        <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                </div>
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-search"></i> Filter</button>
                </div>
            </form>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td><small>{{ \Carbon\Carbon::parse($log->action_date)->format('d M Y H:i') }}</small></td>
                        <td><small>{{ $log->user->full_name ?? 'System' }}</small></td>
                        <td><span class="badge bg-info">{{ $log->action_type }}</span></td>
                        <td><small>{{ $log->table_name }}</small></td>
                        <td><small>{{ $log->description }}</small></td>
                        <td><small>{{ $log->ip_address }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No audit logs found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
VIEWEOF

echo "✓ Created all critical views"

