@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit Vendor</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('vendors.update', $vendor->uid) }}">
            @csrf
            @method('PUT')

            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="vendor_code">Code *</label>
                    <div class="invoice-meta-field">
                        <input id="vendor_code" type="text" name="vendor_code"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('vendor_code', $vendor->vendor_code) }}" required>
                        @error('vendor_code')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="vendor_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="vendor_name" type="text" name="vendor_name"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('vendor_name', $vendor->vendor_name) }}" required>
                        @error('vendor_name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="contact_person">Contact Person</label>
                    <div class="invoice-meta-field">
                        <input id="contact_person" type="text" name="contact_person"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('contact_person', $vendor->contact_person) }}">
                    </div>
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('mobile_no', $vendor->mobile_no) }}">
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="phone_no">Phone</label>
                    <div class="invoice-meta-field">
                        <input id="phone_no" type="text" name="phone_no"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('phone_no', $vendor->phone_no) }}">
                    </div>
                    <label class="invoice-meta-title" for="email">Email</label>
                    <div class="invoice-meta-field">
                        <input id="email" type="email" name="email"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('email', $vendor->email) }}">
                        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="city">City</label>
                    <div class="invoice-meta-field">
                        <input id="city" type="text" name="city"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('city', $vendor->city) }}">
                    </div>
                    <label class="invoice-meta-title" for="ntn">NTN</label>
                    <div class="invoice-meta-field">
                        <input id="ntn" type="text" name="ntn"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('ntn', $vendor->ntn) }}">
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="opening_balance">Opening Balance</label>
                    <div class="invoice-meta-field">
                        <input id="opening_balance" type="number" step="0.01" name="opening_balance"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('opening_balance', $vendor->opening_balance) }}">
                    </div>
                    <label class="invoice-meta-title" for="credit_limit">Credit Limit</label>
                    <div class="invoice-meta-field">
                        <input id="credit_limit" type="number" step="0.01" name="credit_limit"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('credit_limit', $vendor->credit_limit) }}">
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="address">Address</label>
                    <div class="invoice-meta-field">
                        <textarea id="address" name="address" rows="2"
                                  class="form-control form-control-sm invoice-meta-input">{{ old('address', $vendor->address) }}</textarea>
                    </div>
                    <label class="invoice-meta-title" for="is_active">Status</label>
                    <div class="invoice-meta-field">
                        <select id="is_active" name="is_active" class="form-select form-select-sm invoice-meta-input">
                            <option value="1" @selected(old('is_active', $vendor->is_active) == 1)>Active</option>
                            <option value="0" @selected(old('is_active', $vendor->is_active) == 0)>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Update Vendor</button>
                <a href="{{ route('vendors.show', $vendor->uid) }}" class="btn btn-outline-secondary">View</a>
                <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">Back</a>
                <form method="POST" action="{{ route('vendors.destroy', $vendor->uid) }}" class="ms-auto"
                      onsubmit="return confirm('Delete this vendor? If it has purchases it will be deactivated.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
