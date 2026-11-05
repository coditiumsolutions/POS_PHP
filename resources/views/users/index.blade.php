@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Users</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add User</a>
    </div>
    <div class="card"><div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Username</th><th>Full Name</th><th>Role</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->user_name }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td><span class="badge bg-info">{{ $user->role_name }}</span></td>
                    <td><a href="{{ route('users.edit', $user->uid) }}" class="btn btn-sm btn-warning">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $users->links() }}
    </div></div>
</div>
@endsection