@extends('layouts.app')

@section('title', 'Add Warehouse')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Warehouse</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('warehouses.store') }}">
            @csrf
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title">Code</label>
                    <div class="invoice-meta-field">{{ $code }}</div>
                    <label class="invoice-meta-title" for="warehouse_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="warehouse_name" type="text" name="warehouse_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('warehouse_name') }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="location">Location</label>
                    <div class="invoice-meta-field">
                        <input id="location" type="text" name="location" class="form-control form-control-sm invoice-meta-input" value="{{ old('location') }}">
                    </div>
                    <label class="invoice-meta-title" for="contact_person">Contact Person</label>
                    <div class="invoice-meta-field">
                        <input id="contact_person" type="text" name="contact_person" class="form-control form-control-sm invoice-meta-input" value="{{ old('contact_person') }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('mobile_no') }}">
                    </div>
                    <label class="invoice-meta-title"></label>
                    <div class="invoice-meta-field"></div>
                </div>
            </div>
            <input type="hidden" name="is_active" value="1">
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Warehouse</button>
                <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
