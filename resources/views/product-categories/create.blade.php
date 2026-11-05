@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Product Categories</h2>
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Form for creating Product Categories</p>
            <a href="{{ route('product-categories.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
