<?php
$views = [
    // Sales Invoices
    'sales-invoices/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Create Sales Invoice</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'sales-invoices.store\') }}">
            @csrf
            <div class="mb-3"><label>Invoice Date *</label><input type="date" name="invoice_date" class="form-control" value="{{ now()->format(\'Y-m-d\') }}" required></div>
            <button type="submit" class="btn btn-primary">Create Invoice</button>
            <a href="{{ route(\'sales-invoices.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    // Purchase Invoices
    'purchase-invoices/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Create Purchase Invoice</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'purchase-invoices.store\') }}">
            @csrf
            <div class="mb-3"><label>Purchase Date *</label><input type="date" name="purchase_date" class="form-control" value="{{ now()->format(\'Y-m-d\') }}" required></div>
            <button type="submit" class="btn btn-primary">Create Purchase</button>
            <a href="{{ route(\'purchase-invoices.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    // Expenses
    'expenses/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Add Expense</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'expenses.store\') }}">
            @csrf
            <div class="mb-3"><label>Expense Date *</label><input type="date" name="expense_date" class="form-control" value="{{ now()->format(\'Y-m-d\') }}" required></div>
            <div class="mb-3"><label>Amount *</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
            <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
            <button type="submit" class="btn btn-primary">Save Expense</button>
            <a href="{{ route(\'expenses.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    // Customer Payments  
    'customer-payments/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Record Customer Payment</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'customer-payments.store\') }}">
            @csrf
            <div class="mb-3"><label>Payment Date *</label><input type="date" name="payment_date" class="form-control" value="{{ now()->format(\'Y-m-d\') }}" required></div>
            <div class="mb-3"><label>Amount *</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
            <div class="mb-3"><label>Payment Mode *</label><select name="payment_mode" class="form-control" required><option>Cash</option><option>Bank Transfer</option><option>Cheque</option></select></div>
            <button type="submit" class="btn btn-primary">Record Payment</button>
            <a href="{{ route(\'customer-payments.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    // Yogurt Production
    'yogurt-production/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Record Yogurt Production</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'yogurt-production.store\') }}">
            @csrf
            <div class="mb-3"><label>Production Date *</label><input type="date" name="production_date" class="form-control" value="{{ now()->format(\'Y-m-d\') }}" required></div>
            <div class="mb-3"><label>Milk Quantity (kg) *</label><input type="number" name="milk_quantity" class="form-control" step="0.001" required></div>
            <div class="mb-3"><label>Yogurt Quantity (kg) *</label><input type="number" name="yogurt_quantity" class="form-control" step="0.001" required></div>
            <button type="submit" class="btn btn-primary">Record Production</button>
            <a href="{{ route(\'yogurt-production.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    // Monthly Supply
    'monthly-supply/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Add Monthly Supply Template</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'monthly-supply.store\') }}">
            @csrf
            <div class="mb-3"><label>Day of Month *</label><input type="number" name="day_of_month" class="form-control" min="1" max="31" required></div>
            <div class="mb-3"><label>Default Quantity *</label><input type="number" name="default_quantity" class="form-control" step="0.001" required></div>
            <button type="submit" class="btn btn-primary">Save Template</button>
            <a href="{{ route(\'monthly-supply.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',
    
    'monthly-supply/overrides' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Monthly Supply Overrides</h2>
    <div class="card"><div class="card-body">
        <p class="text-muted">Date-specific quantity overrides</p>
        <a href="{{ route(\'monthly-supply.index\') }}" class="btn btn-secondary">← Back to Templates</a>
    </div></div>
</div>
@endsection',

    // Employees
    'employees/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Add Employee</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'employees.store\') }}">
            @csrf
            <div class="mb-3"><label>Employee Code *</label><input type="text" name="employee_code" class="form-control" required></div>
            <div class="mb-3"><label>Employee Name *</label><input type="text" name="employee_name" class="form-control" required></div>
            <div class="mb-3"><label>Basic Salary</label><input type="number" name="basic_salary" class="form-control" step="0.01"></div>
            <button type="submit" class="btn btn-primary">Save Employee</button>
            <a href="{{ route(\'employees.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    'employees/edit' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Edit Employee</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'employees.update\', $employee->uid) }}">
            @csrf
            @method(\'PUT\')
            <div class="mb-3"><label>Employee Code *</label><input type="text" name="employee_code" class="form-control" value="{{ $employee->employee_code }}" required></div>
            <div class="mb-3"><label>Employee Name *</label><input type="text" name="employee_name" class="form-control" value="{{ $employee->employee_name }}" required></div>
            <div class="mb-3"><label>Basic Salary</label><input type="number" name="basic_salary" class="form-control" step="0.01" value="{{ $employee->basic_salary }}"></div>
            <button type="submit" class="btn btn-primary">Update Employee</button>
            <a href="{{ route(\'employees.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    'employees/show' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Employee Details</h2>
    <div class="card"><div class="card-body">
        <p><strong>Code:</strong> {{ $employee->employee_code }}</p>
        <p><strong>Name:</strong> {{ $employee->employee_name }}</p>
        <p><strong>Basic Salary:</strong> ₨ {{ number_format($employee->basic_salary ?? 0, 2) }}</p>
        <a href="{{ route(\'employees.edit\', $employee->uid) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route(\'employees.index\') }}" class="btn btn-secondary">Back</a>
    </div></div>
</div>
@endsection',

    // Users
    'users/index' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Users</h2>
        <a href="{{ route(\'users.create\') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add User</a>
    </div>
    <div class="card"><div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Username</th><th>Full Name</th><th>Role</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->user_name }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td><span class="badge bg-info">{{ $user->role_name }}</span></td>
                    <td><a href="{{ route(\'users.edit\', $user->uid) }}" class="btn btn-sm btn-warning">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $users->links() }}
    </div></div>
</div>
@endsection',

    'users/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Add User</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'users.store\') }}">
            @csrf
            <div class="mb-3"><label>Username *</label><input type="text" name="user_name" class="form-control" required></div>
            <div class="mb-3"><label>Full Name *</label><input type="text" name="full_name" class="form-control" required></div>
            <div class="mb-3"><label>Password *</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label>Role *</label><select name="role_name" class="form-control" required><option value="Administrator">Administrator</option><option value="Manager">Manager</option><option value="Cashier">Cashier</option><option value="Viewer">Viewer</option></select></div>
            <button type="submit" class="btn btn-primary">Save User</button>
            <a href="{{ route(\'users.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    // Branches
    'branches/index' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Branches</h2>
        <a href="{{ route(\'branches.create\') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Branch</a>
    </div>
    <div class="card"><div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Code</th><th>Branch Name</th><th>City</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($branches as $branch)
                <tr>
                    <td>{{ $branch->branch_code }}</td>
                    <td>{{ $branch->branch_name }}</td>
                    <td>{{ $branch->city }}</td>
                    <td><a href="{{ route(\'branches.edit\', $branch->uid) }}" class="btn btn-sm btn-warning">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No branches found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $branches->links() }}
    </div></div>
</div>
@endsection',

    'branches/create' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Add Branch</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'branches.store\') }}">
            @csrf
            <div class="mb-3"><label>Branch Name *</label><input type="text" name="branch_name" class="form-control" required></div>
            <div class="mb-3"><label>City</label><input type="text" name="city" class="form-control"></div>
            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control" rows="3"></textarea></div>
            <button type="submit" class="btn btn-primary">Save Branch</button>
            <a href="{{ route(\'branches.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    'branches/edit' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Edit Branch</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route(\'branches.update\', $branch->uid) }}">
            @csrf
            @method(\'PUT\')
            <div class="mb-3"><label>Branch Code *</label><input type="text" name="branch_code" class="form-control" value="{{ $branch->branch_code }}" required></div>
            <div class="mb-3"><label>Branch Name *</label><input type="text" name="branch_name" class="form-control" value="{{ $branch->branch_name }}" required></div>
            <div class="mb-3"><label>City</label><input type="text" name="city" class="form-control" value="{{ $branch->city }}"></div>
            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control" rows="3">{{ $branch->address }}</textarea></div>
            <button type="submit" class="btn btn-primary">Update Branch</button>
            <a href="{{ route(\'branches.index\') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection',

    // Reports
    'reports/sales' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Sales Report</h2>
    <div class="card"><div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3"><label>From Date</label><input type="date" name="date_from" class="form-control" value="{{ request(\'date_from\') }}"></div>
            <div class="col-md-3"><label>To Date</label><input type="date" name="date_to" class="form-control" value="{{ request(\'date_to\') }}"></div>
            <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button></div>
        </form>
        <p class="text-muted">Sales report data will display here</p>
    </div></div>
</div>
@endsection',

    'reports/purchases' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Purchase Report</h2>
    <div class="card"><div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3"><label>From Date</label><input type="date" name="date_from" class="form-control" value="{{ request(\'date_from\') }}"></div>
            <div class="col-md-3"><label>To Date</label><input type="date" name="date_to" class="form-control" value="{{ request(\'date_to\') }}"></div>
            <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button></div>
        </form>
        <p class="text-muted">Purchase report data will display here</p>
    </div></div>
</div>
@endsection',

    'reports/expenses' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Expense Report</h2>
    <div class="card"><div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3"><label>From Date</label><input type="date" name="date_from" class="form-control" value="{{ request(\'date_from\') }}"></div>
            <div class="col-md-3"><label>To Date</label><input type="date" name="date_to" class="form-control" value="{{ request(\'date_to\') }}"></div>
            <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button></div>
        </form>
        <p class="text-muted">Expense report data will display here</p>
    </div></div>
</div>
@endsection',

    'reports/inventory' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Inventory Report</h2>
    <div class="card"><div class="card-body">
        <p class="text-muted">Inventory stock levels and movements</p>
    </div></div>
</div>
@endsection',

    'reports/branch' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Branch Report</h2>
    <div class="card"><div class="card-body">
        <p class="text-muted">Branch-wise performance data</p>
    </div></div>
</div>
@endsection',

    'reports/user' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>User Report</h2>
    <div class="card"><div class="card-body">
        <p class="text-muted">User-wise sales and performance data</p>
    </div></div>
</div>
@endsection',

    // Inventories
    'inventories/index' => '@extends(\'layouts.app\')
@section(\'content\')
<div class="container-fluid py-4">
    <h2>Inventory Levels</h2>
    <div class="card"><div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Product</th><th>In Stock</th><th>Available</th></tr></thead>
            <tbody>
                @forelse($inventories as $inv)
                <tr>
                    <td>{{ $inv->product->product_name ?? \'N/A\' }}</td>
                    <td class="text-end">{{ number_format($inv->quantity_in_stock, 3) }}</td>
                    <td class="text-end">{{ number_format($inv->quantity_available, 3) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-4 text-muted">No inventory records</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $inventories->links() }}
    </div></div>
</div>
@endsection',
];

foreach ($views as $path => $content) {
    $fullPath = "/workspace/resources/views/{$path}.blade.php";
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($fullPath, $content);
    echo "✓ Created {$path}.blade.php\n";
}

echo "\n✓✓✓ ALL REMAINING VIEWS CREATED!\n";
