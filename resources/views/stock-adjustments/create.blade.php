@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Stock Adjustments</h2>
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Form for creating Stock Adjustments</p>
            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
