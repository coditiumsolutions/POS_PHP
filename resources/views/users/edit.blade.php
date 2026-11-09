@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit User</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('users.update', $user->uid) }}">
            @csrf
            @method('PUT')
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="user_name">Username *</label>
                    <div class="invoice-meta-field">
                        <input id="user_name" type="text" name="user_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('user_name', $user->user_name) }}" required>
                    </div>
                    <label class="invoice-meta-title" for="full_name">Full Name *</label>
                    <div class="invoice-meta-field">
                        <input id="full_name" type="text" name="full_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('full_name', $user->full_name) }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="password">Password</label>
                    <div class="invoice-meta-field">
                        <input id="password" type="password" name="password" class="form-control form-control-sm invoice-meta-input" placeholder="Leave blank to keep">
                    </div>
                    <label class="invoice-meta-title" for="role_name">Role *</label>
                    <div class="invoice-meta-field">
                        <select id="role_name" name="role_name" class="form-select form-select-sm invoice-meta-input" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" @selected(old('role_name', $user->role_name) === $role)>{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="branch">Branch</label>
                    <div class="invoice-meta-field">
                        <select id="branch" name="branch" class="form-select form-select-sm invoice-meta-input">
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_name }}" @selected(old('branch', $user->branch) === $branch->branch_name)>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <label class="invoice-meta-title" for="terminal_name">Terminal</label>
                    <div class="invoice-meta-field">
                        <input id="terminal_name" type="text" name="terminal_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('terminal_name', $user->terminal_name) }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('mobile_no', $user->mobile_no) }}">
                    </div>
                    <label class="invoice-meta-title" for="is_active">Status</label>
                    <div class="invoice-meta-field">
                        <select id="is_active" name="is_active" class="form-select form-select-sm invoice-meta-input">
                            <option value="1" @selected(old('is_active', $user->is_active) == 1)>Active</option>
                            <option value="0" @selected(old('is_active', $user->is_active) == 0)>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="email">Email</label>
                    <div class="invoice-meta-field" style="grid-column: 2 / -1;">
                        <input id="email" type="email" name="email" class="form-control form-control-sm invoice-meta-input" value="{{ old('email', $user->email) }}">
                    </div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
