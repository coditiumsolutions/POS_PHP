@extends('layouts.app')

@section('title', 'Edit Inventory')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit Inventory</h1>
    <p class="text-muted mb-3">One inventory record per product and branch.</p>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('inventories.update', $inventory->uid) }}">
            @csrf
            @method('PUT')

            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="product_uid">Product *</label>
                    <div class="invoice-meta-field">
                        <select id="product_uid" name="product_uid" class="form-select form-select-sm invoice-meta-input" required>
                            @foreach($products as $product)
                                <option value="{{ $product->uid }}" @selected(old('product_uid', $inventory->product_uid) == $product->uid)>
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_uid')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="branch_uid">Branch *</label>
                    <div class="invoice-meta-field">
                        <select id="branch_uid" name="branch_uid" class="form-select form-select-sm invoice-meta-input" required>
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->uid }}" @selected((string) old('branch_uid', $inventory->branch_uid) === (string) $branch->uid)>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_uid')<div class="text-danger small">{{ $message }}</div>@enderror
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
