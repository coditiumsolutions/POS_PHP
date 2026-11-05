@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Products</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, code, or barcode..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category_uid" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->uid }}" {{ request('category_uid') == $category->uid ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">Search</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Purchase Price</th>
                            <th>Sale Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->product_code }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->category ? $product->category->category_name : '-' }}</td>
                            <td>Rs. {{ number_format($product->purchase_price, 2) }}</td>
                            <td>Rs. {{ number_format($product->sale_price, 2) }}</td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('products.edit', $product->uid) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No products found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
