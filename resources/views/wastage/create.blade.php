@extends('layouts.app')

@section('title', 'Add Wastage')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Wastage</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('wastage.store') }}" id="wastageForm">
        @csrf

        <div class="bg-white border rounded p-3 mb-3">
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="wastage_date">Wastage Date *</label>
                    <div class="invoice-meta-field">
                        <input id="wastage_date" type="text" name="wastage_date"
                               class="form-control form-control-sm invoice-meta-input report-date-input"
                               value="{{ old('wastage_date', now()->format('d-M-Y')) }}"
                               placeholder="10-Nov-2026" required autocomplete="off">
                    </div>
                    <label class="invoice-meta-title" for="remarks">Remarks</label>
                    <div class="invoice-meta-field">
                        <input id="remarks" type="text" name="remarks"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('remarks') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Wastage Items</strong>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">Add Item</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Product *</th>
                            <th style="width: 140px;">Quantity *</th>
                            <th>Reason *</th>
                            <th style="width: 48px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <select name="items[0][product_uid]" class="form-select form-select-sm" required>
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->uid }}">{{ $product->product_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-sm" step="0.001" min="0.001" value="1" required>
                            </td>
                            <td>
                                <input type="text" name="items[0][reason]" class="form-control form-control-sm" required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Wastage</button>
            <a href="{{ route('wastage.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr('.report-date-input', { dateFormat: 'd-M-Y', allowInput: true, disableMobile: true });

    let itemCounter = 1;
    const productsHtml = `@foreach($products as $product)<option value="{{ $product->uid }}">{{ $product->product_name }}</option>@endforeach`;

    document.getElementById('addItemBtn').addEventListener('click', function () {
        const html = `
            <tr class="item-row">
                <td>
                    <select name="items[${itemCounter}][product_uid]" class="form-select form-select-sm" required>
                        <option value="">Select product</option>
                        ${productsHtml}
                    </select>
                </td>
                <td><input type="number" name="items[${itemCounter}][quantity]" class="form-control form-control-sm" step="0.001" min="0.001" value="1" required></td>
                <td><input type="text" name="items[${itemCounter}][reason]" class="form-control form-control-sm" required></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button></td>
            </tr>`;
        document.getElementById('itemsBody').insertAdjacentHTML('beforeend', html);
        itemCounter++;
    });

    document.getElementById('itemsBody').addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-item');
        if (!btn) return;
        const rows = document.querySelectorAll('#itemsBody .item-row');
        if (rows.length > 1) btn.closest('.item-row').remove();
    });
});
</script>
@endpush
