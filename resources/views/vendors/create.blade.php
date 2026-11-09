@extends('layouts.app')

@section('title', 'Add Vendor')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Vendor</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('vendors.store') }}">
            @csrf

            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title">Code</label>
                    <div class="invoice-meta-field">{{ $vendorCode }}</div>
                    <label class="invoice-meta-title" for="vendor_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="vendor_name" type="text" name="vendor_name"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('vendor_name') }}" required>
                        @error('vendor_name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="contact_person">Contact Person</label>
                    <div class="invoice-meta-field">
                        <input id="contact_person" type="text" name="contact_person"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('contact_person') }}">
                    </div>
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('mobile_no') }}">
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="phone_no">Phone</label>
                    <div class="invoice-meta-field">
                        <input id="phone_no" type="text" name="phone_no"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('phone_no') }}">
                    </div>
                    <label class="invoice-meta-title" for="email">Email</label>
                    <div class="invoice-meta-field">
                        <input id="email" type="email" name="email"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('email') }}">
                        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="city">City</label>
                    <div class="invoice-meta-field">
                        <input id="city" type="text" name="city"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('city') }}">
                    </div>
                    <label class="invoice-meta-title" for="ntn">NTN</label>
                    <div class="invoice-meta-field">
                        <input id="ntn" type="text" name="ntn"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('ntn') }}">
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="opening_balance">Opening Balance</label>
                    <div class="invoice-meta-field">
                        <input id="opening_balance" type="number" step="0.01" name="opening_balance"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('opening_balance', '0') }}">
                    </div>
                    <label class="invoice-meta-title" for="credit_limit">Credit Limit</label>
                    <div class="invoice-meta-field">
                        <input id="credit_limit" type="number" step="0.01" name="credit_limit"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('credit_limit', '0') }}">
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="address">Address</label>
                    <div class="invoice-meta-field" style="grid-column: 2 / -1;">
                        <textarea id="address" name="address" rows="2"
                                  class="form-control form-control-sm invoice-meta-input">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <input type="hidden" name="is_active" value="1">

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Vendor</button>
                <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
