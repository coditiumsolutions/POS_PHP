@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add Branch</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('branches.store') }}">
            @csrf
            <div class="mb-3"><label>Branch Name *</label><input type="text" name="branch_name" class="form-control" required></div>
            <div class="mb-3"><label>City</label><input type="text" name="city" class="form-control"></div>
            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control" rows="3"></textarea></div>
            <button type="submit" class="btn btn-primary">Save Branch</button>
            <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection