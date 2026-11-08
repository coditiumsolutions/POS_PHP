@extends('layouts.app')

@section('title', 'Inventories')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading mb-1">Inventories</h1>
            <p class="text-muted mb-0">Manage inventory balances.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('inventories.print') }}" class="btn btn-outline-dark" target="_blank" rel="noopener">
                <i class="bi bi-printer" aria-hidden="true"></i> Print Inventory
            </a>
            <a href="{{ route('inventories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle" aria-hidden="true"></i> Add Inventory
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('inventories.index') }}" class="row g-2 align-items-center mb-3">
                <div class="col-md-5 col-lg-4">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search product, code, or branch..."
                           value="{{ $search ?? request('search') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-search" aria-hidden="true"></i> Search
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 inventory-grid">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="inventory-col-branch">Branch</th>
                            <th class="text-end inventory-col-qty">Current Qty</th>
                            <th class="text-nowrap inventory-col-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inv)
                        <tr>
                            <td>{{ $inv->product->product_name ?? ('#'.$inv->product_uid) }}</td>
                            <td class="inventory-col-branch">{{ $inv->branch_name }}</td>
                            <td class="text-end inventory-col-qty">{{ number_format((float) $inv->quantity_in_stock, 3) }}</td>
                            <td class="text-nowrap inventory-col-action">
                                <a href="{{ route('inventories.edit', $inv->uid) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="{{ route('inventories.show', $inv->uid) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No inventory records</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 products-pagination">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
