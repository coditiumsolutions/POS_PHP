@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Edit Branch</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('branches.update', $branch->uid) }}">
            @csrf
            @method('PUT')
            <div class="mb-3"><label>Branch Code *</label><input type="text" name="branch_code" class="form-control" value="{{ $branch->branch_code }}" required></div>
            <div class="mb-3"><label>Branch Name *</label><input type="text" name="branch_name" class="form-control" value="{{ $branch->branch_name }}" required></div>
            <div class="mb-3"><label>City</label><input type="text" name="city" class="form-control" value="{{ $branch->city }}"></div>
            <div class="mb-3"><label>Address</label><textarea name="address" class="form-control" rows="3">{{ $branch->address }}</textarea></div>
            <button type="submit" class="btn btn-primary">Update Branch</button>
            <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection