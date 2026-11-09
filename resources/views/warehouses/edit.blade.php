@extends('layouts.app')

@section('title', 'Edit Warehouse')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit Warehouse</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('warehouses.update', $warehouse->uid) }}">
            @csrf
            @method('PUT')
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="warehouse_code">Code *</label>
                    <div class="invoice-meta-field">
                        <input id="warehouse_code" type="text" name="warehouse_code" class="form-control form-control-sm invoice-meta-input" value="{{ old('warehouse_code', $warehouse->warehouse_code) }}" required>
                    </div>
                    <label class="invoice-meta-title" for="warehouse_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="warehouse_name" type="text" name="warehouse_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('warehouse_name', $warehouse->warehouse_name) }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="location">Location</label>
                    <div class="invoice-meta-field">
                        <input id="location" type="text" name="location" class="form-control form-control-sm invoice-meta-input" value="{{ old('location', $warehouse->location) }}">
                    </div>
                    <label class="invoice-meta-title" for="contact_person">Contact Person</label>
                    <div class="invoice-meta-field">
                        <input id="contact_person" type="text" name="contact_person" class="form-control form-control-sm invoice-meta-input" value="{{ old('contact_person', $warehouse->contact_person) }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('mobile_no', $warehouse->mobile_no) }}">
                    </div>
                    <label class="invoice-meta-title" for="is_active">Status</label>
                    <div class="invoice-meta-field">
                        <select id="is_active" name="is_active" class="form-select form-select-sm invoice-meta-input">
                            <option value="1" @selected(old('is_active', $warehouse->is_active) == 1)>Active</option>
                            <option value="0" @selected(old('is_active', $warehouse->is_active) == 0)>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Warehouse</button>
                <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
