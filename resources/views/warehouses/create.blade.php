@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Warehouses</h2>
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Form for creating Warehouses</p>
            <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
