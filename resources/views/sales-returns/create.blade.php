@extends('layouts.app')

@section('title', 'New Sales Return')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">New Sales Return</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sales-returns.store') }}" id="returnForm">
        @csrf

        @if($originalInvoice)
            <input type="hidden" name="original_invoice_uid" value="{{ $originalInvoice->uid }}">
            <input type="hidden" name="customer_uid" value="{{ $originalInvoice->customer_uid }}">
            <div class="alert alert-info py-2">
                <strong>Original Invoice:</strong> {{ $originalInvoice->invoice_no }} |
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($originalInvoice->invoice_date)->format('d-M-Y') }} |
                <strong>Customer:</strong> {{ $originalInvoice->customer->customer_name ?? 'N/A' }} |
                <strong>Amount:</strong> ₨ {{ number_format($originalInvoice->net_amount, 2) }}
            </div>
        @endif

        <div class="bg-white border rounded p-3 mb-3">
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="return_date">Return Date *</label>
                    <div class="invoice-meta-field">
                        <input id="return_date" type="text" name="return_date"
                               class="form-control form-control-sm invoice-meta-input report-date-input"
                               value="{{ old('return_date', now()->format('d-M-Y')) }}"
                               placeholder="10-Nov-2026" required autocomplete="off">
                    </div>
                    <label class="invoice-meta-title" for="remarks">Remarks</label>
                    <div class="invoice-meta-field">
                        <input id="remarks" type="text" name="remarks"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('remarks') }}" placeholder="Reason for return">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title">Sub Total</label>
                    <div class="invoice-meta-field">
                        <input type="number" name="sub_total" id="sub_total" class="form-control form-control-sm invoice-meta-input" step="0.01" readonly>
                    </div>
                    <label class="invoice-meta-title">Discount Amount</label>
                    <div class="invoice-meta-field">
                        <input type="number" name="discount_amount" id="discount_amount" class="form-control form-control-sm invoice-meta-input" step="0.01" readonly>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title">Tax Amount</label>
                    <div class="invoice-meta-field">
                        <input type="number" name="tax_amount" id="tax_amount" class="form-control form-control-sm invoice-meta-input" step="0.01" readonly>
                    </div>
                    <label class="invoice-meta-title">Net Amount</label>
                    <div class="invoice-meta-field">
                        <input type="number" name="net_amount" id="net_amount" class="form-control form-control-sm invoice-meta-input fw-bold" step="0.01" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Return Items</strong>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">Add Item</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th style="width: 110px;">Quantity</th>
                            <th style="width: 120px;">Unit Price</th>
                            <th style="width: 100px;">Discount %</th>
                            <th style="width: 100px;">Tax %</th>
                            <th style="width: 120px;">Total</th>
                            <th style="width: 48px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @if($originalInvoice)
                            @foreach($originalInvoice->details as $index => $detail)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{ $index }}][product_uid]" class="form-select form-select-sm product-select" required>
                                        <option value="{{ $detail->product_uid }}">{{ $detail->product->product_name }}</option>
                                    </select>
                                </td>
                                <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm item-qty" step="0.001" value="{{ $detail->quantity }}" required></td>
                                <td><input type="number" name="items[{{ $index }}][unit_price]" class="form-control form-control-sm item-price" step="0.01" value="{{ $detail->unit_price }}" required></td>
                                <td><input type="number" name="items[{{ $index }}][discount_percent]" class="form-control form-control-sm item-discount" step="0.01" value="{{ $detail->discount_percent }}" min="0" max="100"></td>
                                <td><input type="number" name="items[{{ $index }}][tax_percent]" class="form-control form-control-sm item-tax" step="0.01" value="{{ $detail->tax_percent }}" min="0"></td>
                                <td><input type="text" class="form-control form-control-sm item-total" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button></td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][product_uid]" class="form-select form-select-sm product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->uid }}" data-price="{{ $product->sale_price }}">{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm item-qty" step="0.001" value="1" required></td>
                                <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm item-price" step="0.01" value="0" required></td>
                                <td><input type="number" name="items[0][discount_percent]" class="form-control form-control-sm item-discount" step="0.01" value="0" min="0" max="100"></td>
                                <td><input type="number" name="items[0][tax_percent]" class="form-control form-control-sm item-tax" step="0.01" value="0" min="0"></td>
                                <td><input type="text" class="form-control form-control-sm item-total" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Process Return</button>
            <a href="{{ route('sales-returns.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
let itemCounter = {{ $originalInvoice ? count($originalInvoice->details) : 1 }};
const productsHtml = `@foreach($products as $product)<option value="{{ $product->uid }}" data-price="{{ $product->sale_price }}">{{ $product->product_name }}</option>@endforeach`;

document.addEventListener('DOMContentLoaded', function () {
    flatpickr('.report-date-input', { dateFormat: 'd-M-Y', allowInput: true, disableMobile: true });
    calculateTotals();

    document.getElementById('addItemBtn').addEventListener('click', addItem);
    document.getElementById('itemsBody').addEventListener('click', function (e) {
        if (e.target.closest('.remove-item')) {
            const rows = document.querySelectorAll('#itemsBody .item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotals();
            }
        }
    });

    document.getElementById('itemsBody').addEventListener('input', calculateTotals);
    document.getElementById('itemsBody').addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const option = e.target.selectedOptions[0];
            const price = option.dataset.price || 0;
            e.target.closest('.item-row').querySelector('.item-price').value = price;
            calculateTotals();
        }
    });
});

function addItem() {
    const html = `
        <tr class="item-row">
            <td>
                <select name="items[${itemCounter}][product_uid]" class="form-select form-select-sm product-select" required>
                    <option value="">Select Product</option>
                    ${productsHtml}
                </select>
            </td>
            <td><input type="number" name="items[${itemCounter}][quantity]" class="form-control form-control-sm item-qty" step="0.001" value="1" required></td>
            <td><input type="number" name="items[${itemCounter}][unit_price]" class="form-control form-control-sm item-price" step="0.01" value="0" required></td>
            <td><input type="number" name="items[${itemCounter}][discount_percent]" class="form-control form-control-sm item-discount" step="0.01" value="0" min="0" max="100"></td>
            <td><input type="number" name="items[${itemCounter}][tax_percent]" class="form-control form-control-sm item-tax" step="0.01" value="0" min="0"></td>
            <td><input type="text" class="form-control form-control-sm item-total" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button></td>
        </tr>`;
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', html);
    itemCounter++;
}

function calculateTotals() {
    let subTotal = 0;
    let totalDiscount = 0;
    let totalTax = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const discountPct = parseFloat(row.querySelector('.item-discount').value) || 0;
        const taxPct = parseFloat(row.querySelector('.item-tax').value) || 0;

        const lineTotal = qty * price;
        const discountAmt = lineTotal * (discountPct / 100);
        const taxAmt = (lineTotal - discountAmt) * (taxPct / 100);
        const itemTotal = lineTotal - discountAmt + taxAmt;

        row.querySelector('.item-total').value = itemTotal.toFixed(2);
        subTotal += lineTotal;
        totalDiscount += discountAmt;
        totalTax += taxAmt;
    });

    document.getElementById('sub_total').value = subTotal.toFixed(2);
    document.getElementById('discount_amount').value = totalDiscount.toFixed(2);
    document.getElementById('tax_amount').value = totalTax.toFixed(2);
    document.getElementById('net_amount').value = (subTotal - totalDiscount + totalTax).toFixed(2);
}
</script>
@endpush
