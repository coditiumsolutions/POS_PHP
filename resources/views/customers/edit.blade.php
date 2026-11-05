@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Edit Customer</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customers.update', $customer->uid) }}">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name *</label>
                        <input type="text" name="customer_name" class="form-control" value="{{ $customer->customer_name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile_no" class="form-control" value="{{ $customer->mobile_no }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3">{{ $customer->address }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection