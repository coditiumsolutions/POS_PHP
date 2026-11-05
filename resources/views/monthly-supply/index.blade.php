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
