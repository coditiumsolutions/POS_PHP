@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Vendor Payments</h2>
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Form for creating Vendor Payments</p>
            <a href="{{ route('vendor-payments.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
