@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit Customer</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('customers.update', $customer->uid) }}">
            @csrf
            @method('PUT')
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="customer_code">Code *</label>
                    <div class="invoice-meta-field">
                        <input id="customer_code" type="text" name="customer_code" class="form-control form-control-sm invoice-meta-input" value="{{ old('customer_code', $customer->customer_code) }}" required>
                    </div>
                    <label class="invoice-meta-title" for="customer_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="customer_name" type="text" name="customer_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('customer_name', $customer->customer_name) }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('mobile_no', $customer->mobile_no) }}">
                    </div>
                    <label class="invoice-meta-title" for="phone_no">Phone</label>
                    <div class="invoice-meta-field">
                        <input id="phone_no" type="text" name="phone_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('phone_no', $customer->phone_no) }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="email">Email</label>
                    <div class="invoice-meta-field">
                        <input id="email" type="email" name="email" class="form-control form-control-sm invoice-meta-input" value="{{ old('email', $customer->email) }}">
                    </div>
                    <label class="invoice-meta-title" for="city">City</label>
                    <div class="invoice-meta-field">
                        <input id="city" type="text" name="city" class="form-control form-control-sm invoice-meta-input" value="{{ old('city', $customer->city) }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="area">Area</label>
                    <div class="invoice-meta-field">
                        <input id="area" type="text" name="area" class="form-control form-control-sm invoice-meta-input" value="{{ old('area', $customer->area) }}">
                    </div>
                    <label class="invoice-meta-title" for="ntn">NTN</label>
                    <div class="invoice-meta-field">
                        <input id="ntn" type="text" name="ntn" class="form-control form-control-sm invoice-meta-input" value="{{ old('ntn', $customer->ntn) }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="opening_balance">Opening Balance</label>
                    <div class="invoice-meta-field">
                        <input id="opening_balance" type="number" step="0.01" name="opening_balance" class="form-control form-control-sm invoice-meta-input" value="{{ old('opening_balance', $customer->opening_balance) }}">
                    </div>
                    <label class="invoice-meta-title" for="credit_limit">Credit Limit</label>
                    <div class="invoice-meta-field">
                        <input id="credit_limit" type="number" step="0.01" name="credit_limit" class="form-control form-control-sm invoice-meta-input" value="{{ old('credit_limit', $customer->credit_limit) }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="address">Address</label>
                    <div class="invoice-meta-field">
                        <textarea id="address" name="address" rows="2" class="form-control form-control-sm invoice-meta-input">{{ old('address', $customer->address) }}</textarea>
                    </div>
                    <label class="invoice-meta-title" for="is_active">Status</label>
                    <div class="invoice-meta-field">
                        <select id="is_active" name="is_active" class="form-select form-select-sm invoice-meta-input">
                            <option value="1" @selected(old('is_active', $customer->is_active) == 1)>Active</option>
                            <option value="0" @selected(old('is_active', $customer->is_active) == 0)>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
