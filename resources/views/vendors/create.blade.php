@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Vendor</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('vendors.store') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Vendor Name *</label>
                        <input type="text" name="vendor_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile_no" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Vendor</button>
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection