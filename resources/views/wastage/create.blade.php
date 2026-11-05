@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Wastage</h2>
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Form for creating Wastage</p>
            <a href="{{ route('wastage.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
