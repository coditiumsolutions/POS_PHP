@extends('layouts.app')

@section('title', 'POS - Cash Sale')

@section('content')
<div class="container-fluid">
    <h1 class="h4 mb-3">POS - Cash Sale</h1>

    <div class="row">
        <!-- Product Selection Section -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="productSearch" class="form-control" 
                               placeholder="Search product by name, code, or barcode...">
                    </div>

                    <div class="row" id="productCategories">
                        @foreach($categories as $category)
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 category-btn" 
                                    data-category="{{ $category->uid }}">
                                {{ $category->category_name }}
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <hr>

                    <div id="productsGrid" class="row">
                        @foreach($products->take(20) as $product)
                        <div class="col-md-3 mb-2 product-item" data-category="{{ $product->category_uid }}">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 add-product"
                                    data-uid="{{ $product->uid }}"
                                    data-name="{{ $product->product_name }}"
                                    data-price="{{ $product->sale_price }}"
                                    data-tax="{{ $product->tax_percent }}"
                                    data-discount="{{ $product->discount_percent }}">
                                <small>{{ $product->product_name }}</small><br>
                                <strong>Rs. {{ number_format($product->sale_price, 2) }}</strong>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <select id="customerSelect" class="form-select">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->uid }}">{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select id="paymentMethodSelect" class="form-select" required>
                            @foreach($paymentMethods as $method)
                            <option value="{{ $method->uid }}">{{ $method->payment_method_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>

                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm" id="cartTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="60">Qty</th>
                                    <th width="80">Price</th>
                                    <th width="80">Total</th>
                                    <th width="30"></th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                                <tr id="emptyCart">
                                    <td colspan="5" class="text-center text-muted">Cart is empty</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <div class="row mb-2">
                        <div class="col-6"><strong>Subtotal:</strong></div>
                        <div class="col-6 text-end" id="subtotalDisplay">Rs. 0.00</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Discount:</strong></div>
                        <div class="col-6 text-end" id="discountDisplay">Rs. 0.00</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Tax:</strong></div>
                        <div class="col-6 text-end" id="taxDisplay">Rs. 0.00</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><h5>Total:</h5></div>
                        <div class="col-6 text-end"><h5 id="totalDisplay">Rs. 0.00</h5></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Customer Given Amount</label>
                        <input type="number" id="customerGivenAmount" class="form-control" 
                               placeholder="0.00" step="0.01">
                        <small class="text-muted">Change: <span id="changeAmount">Rs. 0.00</span></small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" id="completeSaleBtn" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Complete Sale
                        </button>
                        <button type="button" id="clearCartBtn" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = [];

$(document).ready(function() {
    // Product search
    $('#productSearch').on('keyup', function() {
        const search = $(this).val().toLowerCase();
        $('.product-item').each(function() {
            const name = $(this).find('.add-product').data('name').toLowerCase();
            if (name.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Category filter
    $('.category-btn').on('click', function() {
        const categoryUid = $(this).data('category');
        $('.product-item').each(function() {
            if ($(this).data('category') == categoryUid) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
    });

    // Add to cart
    $(document).on('click', '.add-product', function() {
        const product = {
            uid: $(this).data('uid'),
            name: $(this).data('name'),
            price: parseFloat($(this).data('price')),
            tax_percent: parseFloat($(this).data('tax')),
            discount_percent: parseFloat($(this).data('discount')),
            quantity: 1
        };

        const existingIndex = cart.findIndex(item => item.uid === product.uid);
        if (existingIndex >= 0) {
            cart[existingIndex].quantity++;
        } else {
            cart.push(product);
        }

        updateCart();
    });

    // Update quantity
    $(document).on('change', '.qty-input', function() {
        const index = $(this).data('index');
        const qty = parseFloat($(this).val());
        if (qty > 0) {
            cart[index].quantity = qty;
            updateCart();
        }
    });

    // Remove from cart
    $(document).on('click', '.remove-item', function() {
        const index = $(this).data('index');
        cart.splice(index, 1);
        updateCart();
    });

    // Clear cart
    $('#clearCartBtn').on('click', function() {
        if (confirm('Clear all items from cart?')) {
            cart = [];
            updateCart();
        }
    });

    // Calculate change
    $('#customerGivenAmount').on('input', function() {
        const given = parseFloat($(this).val()) || 0;
        const total = parseFloat($('#totalDisplay').text().replace('Rs. ', '').replace(',', ''));
        const change = given - total;
        $('#changeAmount').text('Rs. ' + change.toFixed(2));
    });

    // Complete sale
    $('#completeSaleBtn').on('click', function() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        const paymentMethodUid = $('#paymentMethodSelect').val();
        if (!paymentMethodUid) {
            alert('Please select a payment method!');
            return;
        }

        const subtotal = parseFloat($('#subtotalDisplay').text().replace('Rs. ', '').replace(',', ''));
        const discount = parseFloat($('#discountDisplay').text().replace('Rs. ', '').replace(',', ''));
        const tax = parseFloat($('#taxDisplay').text().replace('Rs. ', '').replace(',', ''));
        const total = parseFloat($('#totalDisplay').text().replace('Rs. ', '').replace(',', ''));

        const saleData = {
            customer_uid: $('#customerSelect').val() || null,
            payment_method_uid: paymentMethodUid,
            items: cart.map(item => ({
                product_uid: item.uid,
                quantity: item.quantity,
                unit_price: item.price,
                discount_percent: item.discount_percent,
                tax_percent: item.tax_percent
            })),
            sub_total: subtotal,
            discount_amount: discount,
            tax_amount: tax,
            net_amount: total,
            paid_amount: total,
            customer_given_amount: parseFloat($('#customerGivenAmount').val()) || null,
        };

        $.ajax({
            url: '{{ route("pos.create-sale") }}',
            method: 'POST',
            data: JSON.stringify(saleData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Sale completed successfully! Invoice: ' + response.invoice_no);
                    cart = [];
                    updateCart();
                    $('#customerSelect').val('');
                    $('#customerGivenAmount').val('');
                    $('#changeAmount').text('Rs. 0.00');
                    
                    if (confirm('Print invoice?')) {
                        window.open('{{ url("pos/print-invoice") }}/' + response.invoice_uid, '_blank');
                    }
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to create sale'));
            }
        });
    });
});

function updateCart() {
    const tbody = $('#cartItems');
    tbody.empty();

    if (cart.length === 0) {
        tbody.append('<tr id="emptyCart"><td colspan="5" class="text-center text-muted">Cart is empty</td></tr>');
        $('#subtotalDisplay').text('Rs. 0.00');
        $('#discountDisplay').text('Rs. 0.00');
        $('#taxDisplay').text('Rs. 0.00');
        $('#totalDisplay').text('Rs. 0.00');
        return;
    }

    let subtotal = 0;
    let totalDiscount = 0;
    let totalTax = 0;

    cart.forEach((item, index) => {
        const lineTotal = item.quantity * item.price;
        const discount = lineTotal * (item.discount_percent / 100);
        const taxable = lineTotal - discount;
        const tax = taxable * (item.tax_percent / 100);
        const itemTotal = taxable + tax;

        subtotal += lineTotal;
        totalDiscount += discount;
        totalTax += tax;

        const row = `
            <tr>
                <td><small>${item.name}</small></td>
                <td><input type="number" class="form-control form-control-sm qty-input" value="${item.quantity}" 
                           min="0.001" step="0.001" data-index="${index}"></td>
                <td><small>Rs. ${item.price.toFixed(2)}</small></td>
                <td><small>Rs. ${itemTotal.toFixed(2)}</small></td>
                <td><button class="btn btn-sm btn-danger remove-item" data-index="${index}">×</button></td>
            </tr>
        `;
        tbody.append(row);
    });

    const netTotal = subtotal - totalDiscount + totalTax;

    $('#subtotalDisplay').text('Rs. ' + subtotal.toFixed(2));
    $('#discountDisplay').text('Rs. ' + totalDiscount.toFixed(2));
    $('#taxDisplay').text('Rs. ' + totalTax.toFixed(2));
    $('#totalDisplay').text('Rs. ' + netTotal.toFixed(2));
}
</script>
@endpush
