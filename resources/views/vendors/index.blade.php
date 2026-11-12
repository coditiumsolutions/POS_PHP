@extends('layouts.app')

@section('title', 'Vendors')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading mb-1">Vendors</h1>
            <p class="text-muted mb-0">Create, view, edit, and delete vendors.</p>
        </div>
        <a href="{{ route('vendors.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Vendor
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('vendors.index') }}" class="row g-2 align-items-center mb-3">
                <div class="col-md-5 col-lg-4">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by name, code, or mobile..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">Search</button>
                    @if(request('search'))
                        <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>Status</th>
                            <th class="text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->vendor_code }}</td>
                            <td>{{ $vendor->vendor_name }}</td>
                            <td>{{ $vendor->contact_person ?: '—' }}</td>
                            <td>{{ $vendor->mobile_no ?: '—' }}</td>
                            <td>{{ $vendor->city ?: '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $vendor->is_active ? 'success' : 'secondary' }}">
                                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('vendors.show', $vendor->uid) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('vendors.edit', $vendor->uid) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('vendors.destroy', $vendor->uid) }}" class="d-inline"
                                      onsubmit="return confirm('Delete this vendor? If it has purchases it will be deactivated.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No vendors found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 products-pagination">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
