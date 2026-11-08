@extends('layouts.app')

@section('title', 'Inventory Details')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Inventory Details</h1>

    <div class="bg-white border rounded p-3">
        <div class="invoice-meta-grid invoice-meta-form invoice-meta-readonly">
            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Product</div>
                <div class="invoice-meta-field">
                    {{ $inventory->product->product_name ?? ('#'.$inventory->product_uid) }}
                    @if($inventory->product?->product_code)
                        <span class="invoice-meta-sub">{{ $inventory->product->product_code }}</span>
                    @endif
                </div>
                <div class="invoice-meta-title">Branch *</div>
                <div class="invoice-meta-field">{{ $inventory->branch_name }}</div>
            </div>

            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Qty In</div>
                <div class="invoice-meta-field">{{ number_format((float) ($inventory->quantity_in ?? 0), 1) }}</div>
                <div class="invoice-meta-title">Qty Out</div>
                <div class="invoice-meta-field">{{ number_format((float) ($inventory->quantity_out ?? 0), 1) }}</div>
            </div>

            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Current Qty</div>
                <div class="invoice-meta-field">{{ number_format((float) $inventory->quantity_in_stock, 1) }}</div>
                <div class="invoice-meta-title">Available</div>
                <div class="invoice-meta-field">{{ number_format((float) $inventory->quantity_available, 1) }}</div>
            </div>

            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Average Cost</div>
                <div class="invoice-meta-field">{{ number_format((float) $inventory->average_cost, 2) }}</div>
                <div class="invoice-meta-title">Reserved</div>
                <div class="invoice-meta-field">{{ number_format((float) ($inventory->quantity_reserved ?? 0), 1) }}</div>
            </div>

            <div class="invoice-meta-row">
                <div class="invoice-meta-title">Product UID</div>
                <div class="invoice-meta-field">{{ $inventory->product_uid }}</div>
                <div class="invoice-meta-title">Updated</div>
                <div class="invoice-meta-field">{{ optional($inventory->updated_date)->format('d-M-Y H:i') ?? '—' }}</div>
            </div>
        </div>

        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('inventories.edit', $inventory->uid) }}" class="btn btn-primary">Edit</a>
            <form method="POST"
                  action="{{ route('inventories.destroy', $inventory->uid) }}"
                  class="d-inline"
                  onsubmit="return confirm('Delete this inventory record? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
