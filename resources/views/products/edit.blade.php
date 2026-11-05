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