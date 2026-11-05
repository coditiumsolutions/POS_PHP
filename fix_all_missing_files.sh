#!/bin/bash
set -e
cd /workspace

echo "Creating ALL missing views and fixing controllers..."

# Run migration to add action_type
php artisan migrate --force

# Create comprehensive views script
cat > /tmp/create_all_views.php << 'VIEWSPHP'
<?php
$views = [
    // Products
    'products/create' => <<<'VIEW'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Product</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Product Code *</label>
                        <input type="text" name="product_code" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="unit_price" class="form-control" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sale Price</label>
                        <input type="number" name="sale_price" class="form-control" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
VIEW
    ,
    
    'products/edit' => <<<'VIEW'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Edit Product</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product->uid) }}">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Product Code *</label>
                        <input type="text" name="product_code" class="form-control" value="{{ $product->product_code }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="product_name" class="form-control" value="{{ $product->product_name }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="unit_price" class="form-control" step="0.01" value="{{ $product->unit_price }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sale Price</label>
                        <input type="number" name="sale_price" class="form-control" step="0.01" value="{{ $product->sale_price }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="{{ $product->barcode }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
VIEW
    ,
    
    'customers/create' => <<<'VIEW'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Customer</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customers.store') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name *</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile_no" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
VIEW
    ,

    'customers/edit' => <<<'VIEW'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Edit Customer</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customers.update', $customer->uid) }}">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name *</label>
                        <input type="text" name="customer_name" class="form-control" value="{{ $customer->customer_name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile_no" class="form-control" value="{{ $customer->mobile_no }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3">{{ $customer->address }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
VIEW
    ,

    'vendors/create' => <<<'VIEW'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Vendor</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('vendors.store') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Vendor Name *</label>
                        <input type="text" name="vendor_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile_no" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Vendor</button>
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
VIEW
    ,
    
    'vendors/edit' => <<<'VIEW'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Edit Vendor</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('vendors.update', $vendor->uid) }}">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Vendor Name *</label>
                        <input type="text" name="vendor_name" class="form-control" value="{{ $vendor->vendor_name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile_no" class="form-control" value="{{ $vendor->mobile_no }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3">{{ $vendor->address }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Vendor</button>
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
VIEW
    ,
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

echo "\nAll views created!\n";
VIEWSPHP

php /tmp/create_all_views.php

echo "✓ Phase 1 views created"
echo "Script completed successfully!"

