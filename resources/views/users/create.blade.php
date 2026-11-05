@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add User</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="mb-3"><label>Username *</label><input type="text" name="user_name" class="form-control" required></div>
            <div class="mb-3"><label>Full Name *</label><input type="text" name="full_name" class="form-control" required></div>
            <div class="mb-3"><label>Password *</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-3"><label>Role *</label><select name="role_name" class="form-control" required><option value="Administrator">Administrator</option><option value="Manager">Manager</option><option value="Cashier">Cashier</option><option value="Viewer">Viewer</option></select></div>
            <button type="submit" class="btn btn-primary">Save User</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>
@endsection