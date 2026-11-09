@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add User</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="user_name">Username *</label>
                    <div class="invoice-meta-field">
                        <input id="user_name" type="text" name="user_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('user_name') }}" required>
                    </div>
                    <label class="invoice-meta-title" for="full_name">Full Name *</label>
                    <div class="invoice-meta-field">
                        <input id="full_name" type="text" name="full_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('full_name') }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="password">Password *</label>
                    <div class="invoice-meta-field">
                        <input id="password" type="password" name="password" class="form-control form-control-sm invoice-meta-input" required>
                    </div>
                    <label class="invoice-meta-title" for="role_name">Role *</label>
                    <div class="invoice-meta-field">
                        <select id="role_name" name="role_name" class="form-select form-select-sm invoice-meta-input" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" @selected(old('role_name') === $role)>{{ $role }}</option>
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
                                <option value="{{ $branch->branch_name }}" @selected(old('branch') === $branch->branch_name)>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <label class="invoice-meta-title" for="terminal_name">Terminal</label>
                    <div class="invoice-meta-field">
                        <input id="terminal_name" type="text" name="terminal_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('terminal_name') }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('mobile_no') }}">
                    </div>
                    <label class="invoice-meta-title" for="email">Email</label>
                    <div class="invoice-meta-field">
                        <input id="email" type="email" name="email" class="form-control form-control-sm invoice-meta-input" value="{{ old('email') }}">
                    </div>
                </div>
            </div>
            <input type="hidden" name="is_active" value="1">
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save User</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
