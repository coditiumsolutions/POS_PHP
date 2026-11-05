@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Vendor Payments</h2>
        <a href="{{ route('vendor-payments.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Vendor Payments listing will display here</p>
        </div>
    </div>
</div>
@endsection
