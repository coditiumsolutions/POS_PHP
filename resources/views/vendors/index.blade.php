@extends('layouts.app')

@section('title', 'Vendors')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Vendors</h1>
        <a href="{{ route('vendors.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Vendor
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
                    <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->vendor_code }}</td>
                            <td>{{ $vendor->vendor_name }}</td>
                            <td>{{ $vendor->contact_person }}</td>
                            <td>{{ $vendor->mobile_no }}</td>
                            <td>{{ $vendor->city }}</td>
                            <td>
                                @if($vendor->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('vendors.show', $vendor->uid) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('vendors.edit', $vendor->uid) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No vendors found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $vendors->links() }}
        </div>
    </div>
</div>
@endsection
