@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2>New Sales Return</h2>
        <p class="text-muted">Process a product return and restore stock</p>
    </div>

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
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Return Details</h5>
                    </div>
                    <div class="card-body">
                        @if($originalInvoice)
                        <input type="hidden" name="original_invoice_uid" value="{{ $originalInvoice->uid }}">
                        <input type="hidden" name="customer_uid" value="{{ $originalInvoice->customer_uid }}">
                        
                        <div class="alert alert-info">
                            <strong>Original Invoice:</strong> {{ $originalInvoice->invoice_no }} |
                            <strong>Date:</strong> {{ \Carbon\Carbon::parse($originalInvoice->invoice_date)->format('d M Y') }} |
                            <strong>Customer:</strong> {{ $originalInvoice->customer->customer_name ?? 'N/A' }} |
                            <strong>Amount:</strong> ₨ {{ number_format($originalInvoice->net_amount, 2) }}
                        </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Return Date <span class="text-danger">*</span></label>
                                <input type="date" name="return_date" class="form-control" value="{{ old('return_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}" placeholder="Reason for return">
                            </div>
                        </div>

                        <h6 class="mt-4 mb-3">Return Items</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30%">Product</th>
                                        <th width="12%">Quantity</th>
                                        <th width="15%">Unit Price</th>
                                        <th width="12%">Discount %</th>
                                        <th width="12%">Tax %</th>
                                        <th width="15%">Total</th>
                                        <th width="4%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    @if($originalInvoice)
                                        @foreach($originalInvoice->details as $index => $detail)
                                        <tr class="item-row">
                                            <td>
                                                <select name="items[{{$index}}][product_uid]" class="form-select product-select" required>
                                                    <option value="{{ $detail->product_uid }}">{{ $detail->product->product_name }}</option>
                                                </select>
                                            </td>
                                            <td><input type="number" name="items[{{$index}}][quantity]" class="form-control item-qty" step="0.001" value="{{ $detail->quantity }}" required></td>
                                            <td><input type="number" name="items[{{$index}}][unit_price]" class="form-control item-price" step="0.01" value="{{ $detail->unit_price }}" required></td>
                                            <td><input type="number" name="items[{{$index}}][discount_percent]" class="form-control item-discount" step="0.01" value="{{ $detail->discount_percent }}" min="0" max="100"></td>
                                            <td><input type="number" name="items[{{$index}}][tax_percent]" class="form-control item-tax" step="0.01" value="{{ $detail->tax_percent }}" min="0"></td>
                                            <td><input type="text" class="form-control item-total" readonly></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[0][product_uid]" class="form-select product-select" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                <option value="{{ $product->uid }}" data-price="{{ $product->sale_price }}">{{ $product->product_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="items[0][quantity]" class="form-control item-qty" step="0.001" value="1" required></td>
                                        <td><input type="number" name="items[0][unit_price]" class="form-control item-price" step="0.01" value="0" required></td>
                                        <td><input type="number" name="items[0][discount_percent]" class="form-control item-discount" step="0.01" value="0" min="0" max="100"></td>
                                        <td><input type="number" name="items[0][tax_percent]" class="form-control item-tax" step="0.01" value="0" min="0"></td>
                                        <td><input type="text" class="form-control item-total" readonly></td>
                                        <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-times"></i></button></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" id="addItemBtn"><i class="fas fa-plus"></i> Add Item</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Sub Total</label>
                            <input type="number" name="sub_total" id="sub_total" class="form-control" step="0.01" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount Amount</label>
                            <input type="number" name="discount_amount" id="discount_amount" class="form-control" step="0.01" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tax Amount</label>
                            <input type="number" name="tax_amount" id="tax_amount" class="form-control" step="0.01" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Net Amount</strong></label>
                            <input type="number" name="net_amount" id="net_amount" class="form-control fw-bold" step="0.01" readonly>
                        </div>
                        
                        <hr>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check"></i> Process Return
                        </button>
                        <a href="{{ route('sales-returns.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemCounter = {{ $originalInvoice ? count($originalInvoice->details) : 1 }};

document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
    
    document.getElementById('addItemBtn').addEventListener('click', addItem);
    document.getElementById('itemsBody').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('.item-row').remove();
            calculateTotals();
        }
    });
    
    document.getElementById('itemsBody').addEventListener('input', calculateTotals);
    document.getElementById('itemsBody').addEventListener('change', function(e) {
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
                <select name="items[${itemCounter}][product_uid]" class="form-select product-select" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                    <option value="{{ $product->uid }}" data-price="{{ $product->sale_price }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[${itemCounter}][quantity]" class="form-control item-qty" step="0.001" value="1" required></td>
            <td><input type="number" name="items[${itemCounter}][unit_price]" class="form-control item-price" step="0.01" value="0" required></td>
            <td><input type="number" name="items[${itemCounter}][discount_percent]" class="form-control item-discount" step="0.01" value="0" min="0" max="100"></td>
            <td><input type="number" name="items[${itemCounter}][tax_percent]" class="form-control item-tax" step="0.01" value="0" min="0"></td>
            <td><input type="text" class="form-control item-total" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-times"></i></button></td>
        </tr>
    `;
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
        const taxableAmt = lineTotal - discountAmt;
        const taxAmt = taxableAmt * (taxPct / 100);
        const itemTotal = taxableAmt + taxAmt;
        
        row.querySelector('.item-total').value = itemTotal.toFixed(2);
        
        subTotal += lineTotal;
        totalDiscount += discountAmt;
        totalTax += taxAmt;
    });
    
    const netAmount = subTotal - totalDiscount + totalTax;
    
    document.getElementById('sub_total').value = subTotal.toFixed(2);
    document.getElementById('discount_amount').value = totalDiscount.toFixed(2);
    document.getElementById('tax_amount').value = totalTax.toFixed(2);
    document.getElementById('net_amount').value = netAmount.toFixed(2);
}
</script>
@endsection
