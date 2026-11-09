@extends('layouts.app')

@section('title', 'Add Branch')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Branch</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('branches.store') }}">
            @csrf
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title">Code</label>
                    <div class="invoice-meta-field">{{ $branchCode }}</div>
                    <label class="invoice-meta-title" for="branch_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="branch_name" type="text" name="branch_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('branch_name') }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="city">City</label>
                    <div class="invoice-meta-field">
                        <input id="city" type="text" name="city" class="form-control form-control-sm invoice-meta-input" value="{{ old('city') }}">
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
                    <label class="invoice-meta-title" for="phone_no">Phone</label>
                    <div class="invoice-meta-field">
                        <input id="phone_no" type="text" name="phone_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('phone_no') }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="email">Email</label>
                    <div class="invoice-meta-field">
                        <input id="email" type="email" name="email" class="form-control form-control-sm invoice-meta-input" value="{{ old('email') }}">
                    </div>
                    <label class="invoice-meta-title" for="address">Address</label>
                    <div class="invoice-meta-field">
                        <input id="address" type="text" name="address" class="form-control form-control-sm invoice-meta-input" value="{{ old('address') }}">
                    </div>
                </div>
            </div>
            <input type="hidden" name="is_active" value="1">
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Branch</button>
                <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
