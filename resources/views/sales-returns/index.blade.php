@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Sales Returns</h2>
        <a href="{{ route('sales-returns.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Return
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Invoice No, Remarks..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('sales-returns.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Return No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>User</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                        <tr>
                            <td><strong>{{ $return->invoice_no }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($return->invoice_date)->format('d M Y') }}</td>
                            <td>{{ $return->customer->customer_name ?? 'Walk-in' }}</td>
                            <td class="text-end"><strong>₨ {{ number_format($return->net_amount, 2) }}</strong></td>
                            <td><small>{{ $return->user_name }}</small></td>
                            <td><small>{{ $return->remarks }}</small></td>
                            <td>
                                <a href="{{ route('sales-returns.show', $return->uid) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No sales returns found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end">₨ {{ number_format($totalAmount ?? 0, 2) }}</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-3">
                {{ $returns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
