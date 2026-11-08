@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Product</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf

            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="product_code">Code *</label>
                    <div class="invoice-meta-field">
                        <input id="product_code" type="text" name="product_code"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('product_code') }}" required>
                        @error('product_code')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="product_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="product_name" type="text" name="product_name"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('product_name') }}" required>
                        @error('product_name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="category_uid">Category *</label>
                    <div class="invoice-meta-field">
                        <select id="category_uid" name="category_uid" class="form-select form-select-sm invoice-meta-input" required>
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->uid }}" @selected(old('category_uid') == $category->uid)>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_uid')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="branch_uid">Branch *</label>
                    <div class="invoice-meta-field">
                        @php
                            $defaultBranchUid = old('branch_uid');
                            if ($defaultBranchUid === null) {
                                $userBranch = auth()->user()->branch ?? null;
                                $defaultBranchUid = optional($branches->firstWhere('branch_name', $userBranch))->uid;
                            }
                        @endphp
                        <select id="branch_uid" name="branch_uid" class="form-select form-select-sm invoice-meta-input" required>
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->uid }}" @selected((string) $defaultBranchUid === (string) $branch->uid)>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_uid')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="barcode">Barcode</label>
                    <div class="invoice-meta-field">
                        <input id="barcode" type="text" name="barcode"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('barcode') }}">
                        @error('barcode')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title"></label>
                    <div class="invoice-meta-field"></div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="purchase_price">Purchase Price *</label>
                    <div class="invoice-meta-field">
                        <input id="purchase_price" type="number" step="0.01" min="0" name="purchase_price"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('purchase_price', '0') }}" required>
                        @error('purchase_price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="sale_price">Sale Price *</label>
                    <div class="invoice-meta-field">
                        <input id="sale_price" type="number" step="0.01" min="0" name="sale_price"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('sale_price', '0') }}" required>
                        @error('sale_price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <input type="hidden" name="is_active" value="1">

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
