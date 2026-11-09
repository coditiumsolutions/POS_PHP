@extends('layouts.app')

@section('title', 'Add Monthly Supply Template')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Monthly Supply Template</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('monthly-supply.store') }}">
            @csrf
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="customer_uid">Customer *</label>
                    <div class="invoice-meta-field">
                        <select id="customer_uid" name="customer_uid" class="form-select form-select-sm invoice-meta-input" required>
                            <option value="">Select customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->uid }}" @selected(old('customer_uid') == $customer->uid)>
                                    {{ $customer->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <label class="invoice-meta-title" for="product_uid">Product *</label>
                    <div class="invoice-meta-field">
                        <select id="product_uid" name="product_uid" class="form-select form-select-sm invoice-meta-input" required>
                            <option value="">Select product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->uid }}" @selected(old('product_uid') == $product->uid)>
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="day_of_month">Day of Month *</label>
                    <div class="invoice-meta-field">
                        <input id="day_of_month" type="number" name="day_of_month" min="1" max="31"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('day_of_month') }}" required>
                    </div>
                    <label class="invoice-meta-title" for="default_quantity">Default Quantity *</label>
                    <div class="invoice-meta-field">
                        <input id="default_quantity" type="number" name="default_quantity" step="0.001" min="0.001"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('default_quantity') }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="is_active">Status</label>
                    <div class="invoice-meta-field">
                        <select id="is_active" name="is_active" class="form-select form-select-sm invoice-meta-input">
                            <option value="1" @selected(old('is_active', '1') == '1')>Active</option>
                            <option value="0" @selected(old('is_active') == '0')>Inactive</option>
                        </select>
                    </div>
                    <label class="invoice-meta-title"></label>
                    <div class="invoice-meta-field"></div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Template</button>
                <a href="{{ route('monthly-supply.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
