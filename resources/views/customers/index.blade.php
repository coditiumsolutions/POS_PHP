@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Customers</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Customer
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, code, or mobile..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary">Search</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>Credit Limit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->customer_code }}</td>
                            <td>{{ $customer->customer_name }}</td>
                            <td>{{ $customer->mobile_no }}</td>
                            <td>{{ $customer->city }}</td>
                            <td>Rs. {{ number_format($customer->credit_limit, 2) }}</td>
                            <td>
                                @if($customer->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('customers.edit', $customer->uid) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No customers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
