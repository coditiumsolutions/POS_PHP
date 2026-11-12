@extends('layouts.app')

@section('title', 'Edit Inventory')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit Inventory</h1>
    <p class="text-muted mb-3">Update quantity and cost for this product/branch. Product and branch cannot be changed here.</p>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('inventories.update', $inventory->uid) }}">
            @csrf
            @method('PUT')

            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title">Product</label>
                    <div class="invoice-meta-field">
                        <input type="text" class="form-control form-control-sm invoice-meta-input"
                               value="{{ $inventory->product->product_name ?? ('#'.$inventory->product_uid) }}" readonly>
                    </div>
                    <label class="invoice-meta-title">Branch</label>
                    <div class="invoice-meta-field">
                        <input type="text" class="form-control form-control-sm invoice-meta-input"
                               value="{{ $inventory->branch->branch_name ?? $inventory->branch_name }}" readonly>
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="quantity_in_stock">Current Qty *</label>
                    <div class="invoice-meta-field">
                        <input id="quantity_in_stock" type="number" step="0.001" name="quantity_in_stock"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('quantity_in_stock', $inventory->quantity_in_stock) }}" required>
                        @error('quantity_in_stock')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="average_cost">Average Cost</label>
                    <div class="invoice-meta-field">
                        <input id="average_cost" type="number" step="0.01" name="average_cost"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('average_cost', $inventory->average_cost) }}">
                        @error('average_cost')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">Back to list</a>
            </div>
        </form>
    </div>
</div>
@endsection
