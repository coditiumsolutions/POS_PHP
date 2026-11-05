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
