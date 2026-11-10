@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">User Details</h1>

    <div class="bg-white border rounded p-3 mb-3">
        <div class="invoice-meta-grid invoice-meta-form invoice-meta-readonly">
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Username</div>
                <div class="invoice-meta-field">{{ $user->user_name }}</div>
                <div class="invoice-meta-title">Full Name</div>
                <div class="invoice-meta-field">{{ $user->full_name }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Role</div>
                <div class="invoice-meta-field">{{ $user->role_name }}</div>
                <div class="invoice-meta-title">Status</div>
                <div class="invoice-meta-field">
                    @if($user->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Branch</div>
                <div class="invoice-meta-field">{{ $user->branch ?: '—' }}</div>
                <div class="invoice-meta-title">Terminal</div>
                <div class="invoice-meta-field">{{ $user->terminal_name ?: '—' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Mobile</div>
                <div class="invoice-meta-field">{{ $user->mobile_no ?: '—' }}</div>
                <div class="invoice-meta-title">Email</div>
                <div class="invoice-meta-field">{{ $user->email ?: '—' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Last Login</div>
                <div class="invoice-meta-field">
                    {{ $user->last_login_date ? $user->last_login_date->format('d-M-Y H:i') : '—' }}
                </div>
                <div class="invoice-meta-title">Sales Invoices</div>
                <div class="invoice-meta-field">{{ number_format($salesCount) }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Created</div>
                <div class="invoice-meta-field">
                    {{ $user->created_date ? $user->created_date->format('d-M-Y H:i') : '—' }}
                </div>
                <div class="invoice-meta-title">Updated</div>
                <div class="invoice-meta-field">
                    {{ $user->updated_date ? $user->updated_date->format('d-M-Y H:i') : '—' }}
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex flex-wrap gap-2">
            <a href="{{ route('users.edit', $user->uid) }}" class="btn btn-primary">Edit</a>
            <form method="POST" action="{{ route('users.destroy', $user->uid) }}"
                  onsubmit="return confirm('Delete this user? If the user has sales history they will be deactivated instead.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        @disabled((int) auth()->id() === (int) $user->uid)>
                    Delete
                </button>
            </form>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        @if((int) auth()->id() === (int) $user->uid)
            <div class="text-muted small mt-2">You cannot delete the account you are currently logged in with.</div>
        @elseif($salesCount > 0)
            <div class="text-muted small mt-2">This user has sales history, so Delete will deactivate the account instead of removing it permanently.</div>
        @endif
    </div>
</div>
@endsection
