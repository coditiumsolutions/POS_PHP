@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Warehouses</h2>
        <a href="{{ route('warehouses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Warehouses listing will display here</p>
        </div>
    </div>
</div>
@endsection
