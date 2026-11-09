@extends('layouts.app')

@section('title', 'Vendor Details')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Vendor Details</h1>

    <div class="bg-white border rounded p-3 mb-3">
        <div class="invoice-meta-grid invoice-meta-form invoice-meta-readonly">
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Code</div>
                <div class="invoice-meta-field">{{ $vendor->vendor_code }}</div>
                <div class="invoice-meta-title">Name</div>
                <div class="invoice-meta-field">{{ $vendor->vendor_name }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Contact</div>
                <div class="invoice-meta-field">{{ $vendor->contact_person ?: '—' }}</div>
                <div class="invoice-meta-title">Mobile</div>
                <div class="invoice-meta-field">{{ $vendor->mobile_no ?: '—' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Phone</div>
                <div class="invoice-meta-field">{{ $vendor->phone_no ?: '—' }}</div>
                <div class="invoice-meta-title">Email</div>
                <div class="invoice-meta-field">{{ $vendor->email ?: '—' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">City</div>
                <div class="invoice-meta-field">{{ $vendor->city ?: '—' }}</div>
                <div class="invoice-meta-title">NTN</div>
                <div class="invoice-meta-field">{{ $vendor->ntn ?: '—' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Address</div>
                <div class="invoice-meta-field">{{ $vendor->address ?: '—' }}</div>
                <div class="invoice-meta-title">Status</div>
                <div class="invoice-meta-field">{{ $vendor->is_active ? 'Active' : 'Inactive' }}</div>
            </div>
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Purchases</div>
                <div class="invoice-meta-field">Rs. {{ number_format($totalPurchases, 2) }}</div>
                <div class="invoice-meta-title">Paid / Balance</div>
                <div class="invoice-meta-field">
                    Rs. {{ number_format($totalPaid, 2) }}
                    <span class="invoice-meta-sub">Balance: Rs. {{ number_format($balance, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex flex-wrap gap-2">
            <a href="{{ route('vendors.edit', $vendor->uid) }}" class="btn btn-primary">Edit</a>
            <form method="POST" action="{{ route('vendors.destroy', $vendor->uid) }}"
                  onsubmit="return confirm('Delete this vendor? If it has purchases it will be deactivated.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
