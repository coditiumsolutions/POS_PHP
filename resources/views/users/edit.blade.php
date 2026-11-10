@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit User</h1>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Could not update this user:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('users.update', $user->uid) }}">
            @csrf
            @method('PUT')
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="user_name">Username *</label>
                    <div class="invoice-meta-field">
                        <input id="user_name" type="text" name="user_name"
                               class="form-control form-control-sm invoice-meta-input {{ $errors->has('user_name') ? 'is-invalid' : '' }}"
                               value="{{ old('user_name', $user->user_name) }}" required maxlength="100" autocomplete="off">
                        @error('user_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="full_name">Full Name *</label>
                    <div class="invoice-meta-field">
                        <input id="full_name" type="text" name="full_name"
                               class="form-control form-control-sm invoice-meta-input {{ $errors->has('full_name') ? 'is-invalid' : '' }}"
                               value="{{ old('full_name', $user->full_name) }}" required maxlength="200">
                        @error('full_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="password">Password</label>
                    <div class="invoice-meta-field">
                        <input id="password" type="password" name="password"
                               class="form-control form-control-sm invoice-meta-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="Leave blank to keep" minlength="6" maxlength="100" autocomplete="new-password">
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="role_name">Role *</label>
                    <div class="invoice-meta-field">
                        <select id="role_name" name="role_name"
                                class="form-select form-select-sm invoice-meta-input {{ $errors->has('role_name') ? 'is-invalid' : '' }}"
                                required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" @selected(old('role_name', $user->role_name) === $role)>{{ $role }}</option>
                            @endforeach
                        </select>
                        @error('role_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="branch">Branch</label>
                    <div class="invoice-meta-field">
                        <select id="branch" name="branch"
                                class="form-select form-select-sm invoice-meta-input {{ $errors->has('branch') ? 'is-invalid' : '' }}">
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_name }}" @selected(old('branch', $user->branch) === $branch->branch_name)>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="terminal_name">Terminal</label>
                    <div class="invoice-meta-field">
                        <input id="terminal_name" type="text" name="terminal_name"
                               class="form-control form-control-sm invoice-meta-input {{ $errors->has('terminal_name') ? 'is-invalid' : '' }}"
                               value="{{ old('terminal_name', $user->terminal_name) }}" maxlength="100">
                        @error('terminal_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no"
                               class="form-control form-control-sm invoice-meta-input {{ $errors->has('mobile_no') ? 'is-invalid' : '' }}"
                               value="{{ old('mobile_no', $user->mobile_no) }}" maxlength="30">
                        @error('mobile_no')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="is_active">Status</label>
                    <div class="invoice-meta-field">
                        <select id="is_active" name="is_active"
                                class="form-select form-select-sm invoice-meta-input {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
                            <option value="1" @selected(old('is_active', $user->is_active) == 1)>Active</option>
                            <option value="0" @selected(old('is_active', $user->is_active) == 0)>Inactive</option>
                        </select>
                        @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="email">Email</label>
                    <div class="invoice-meta-field" style="grid-column: 2 / -1;">
                        <input id="email" type="email" name="email"
                               class="form-control form-control-sm invoice-meta-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email', $user->email) }}" maxlength="150">
                        @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
