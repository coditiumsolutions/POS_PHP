@extends('layouts.app')

@section('title', $pageTitle ?? 'POS - Cash Sale')

@section('content')
@php $barcodeMode = !empty($enableBarcodeScan); @endphp
<div class="container-fluid">
    <h1 class="h4 mb-3 page-heading" style="font-size:1.5rem;">{{ $pageTitle ?? 'POS - Cash Sale' }}</h1>

    <div id="posAlert" class="alert d-none" role="alert"></div>

    <div class="row pos-sale-layout {{ $barcodeMode ? 'pos-sale-layout--barcode-only' : '' }}" id="posSaleLayout">
        <!-- Product / Barcode Selection -->
        <div class="col-md-7 {{ $barcodeMode ? 'pos-sale-panel--barcode-only' : '' }}">
            <div class="card h-100">
                <div class="card-body">
                    @if($barcodeMode)
                        <div class="pos-barcode-center">
                            <div class="pos-barcode-scan-wrap">
                                <label class="form-label small text-muted mb-1" for="posBarcodeScan">Barcode</label>
                                <input type="text"
                                       id="posBarcodeScan"
                                       class="form-control form-control-sm pos-barcode-scan-input"
                                       placeholder="Scan barcode…"
                                       autocomplete="off"
                                       autofocus
                                       inputmode="none"
                                       spellcheck="false" />
                            </div>
                            <p class="pos-barcode-hint text-muted small mb-0">
                                Keep this field focused. Scan to add. Same barcode waits 2 seconds before it can add again.
                            </p>
                        </div>
                        {{-- Hidden catalog for barcode/code lookup — no item grid on this screen (POS_MT parity). --}}
                        <div id="productsCatalog" class="d-none" aria-hidden="true">
                            @foreach($products as $product)
                            <div class="pos-product-row"
                                 data-uid="{{ $product->uid }}"
                                 data-code="{{ $product->product_code }}"
                                 data-barcode="{{ $product->barcode ?: $product->product_code }}"
                                 data-name="{{ $product->product_name }}"
                                 data-price="{{ $product->sale_price }}"
                                 data-tax="{{ $product->tax_percent }}"
                                 data-discount="{{ $product->discount_percent }}"></div>
                            @endforeach
                        </div>
                        @if($products->isEmpty())
                            <div class="p-3 text-muted">No active products found.</div>
                        @endif
                    @else
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
                            @foreach($products->take(40) as $product)
                            <div class="col-md-3 mb-2 product-item" data-category="{{ $product->category_uid }}">
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 add-product"
                                        data-uid="{{ $product->uid }}"
                                        data-name="{{ $product->product_name }}"
                                        data-barcode="{{ $product->barcode }}"
                                        data-code="{{ $product->product_code }}"
                                        data-price="{{ $product->sale_price }}"
                                        data-tax="{{ $product->tax_percent }}"
                                        data-discount="{{ $product->discount_percent }}">
                                    <small>{{ $product->product_name }}</small><br>
                                    <strong>Rs. {{ number_format($product->sale_price, 2) }}</strong>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="col-md-5">
            <div class="card h-100">
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
const barcodeMode = {{ $barcodeMode ? 'true' : 'false' }};

function showAlert(type, message) {
    const el = document.getElementById('posAlert');
    if (!el) { alert(message); return; }
    el.className = 'alert alert-' + type;
    el.textContent = message;
    el.classList.remove('d-none');
    window.setTimeout(function () { el.classList.add('d-none'); }, 2500);
}

function addProductToCart(product) {
    const existingIndex = cart.findIndex(item => String(item.uid) === String(product.uid));
    if (existingIndex >= 0) {
        cart[existingIndex].quantity++;
    } else {
        cart.push({
            uid: product.uid,
            name: product.name,
            price: parseFloat(product.price) || 0,
            tax_percent: parseFloat(product.tax_percent) || 0,
            discount_percent: parseFloat(product.discount_percent) || 0,
            quantity: 1
        });
    }
    updateCart();
}

$(document).ready(function() {
    if (!barcodeMode) {
        $('#productSearch').on('keyup', function() {
            const search = $(this).val().toLowerCase();
            $('.product-item').each(function() {
                const btn = $(this).find('.add-product');
                const name = String(btn.data('name') || '').toLowerCase();
                const code = String(btn.data('code') || '').toLowerCase();
                const barcode = String(btn.data('barcode') || '').toLowerCase();
                if (name.includes(search) || code.includes(search) || barcode.includes(search)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

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

        $(document).on('click', '.add-product', function() {
            addProductToCart({
                uid: $(this).data('uid'),
                name: $(this).data('name'),
                price: $(this).data('price'),
                tax_percent: $(this).data('tax'),
                discount_percent: $(this).data('discount')
            });
        });
    }

    $(document).on('change', '.qty-input', function() {
        const index = $(this).data('index');
        const qty = parseFloat($(this).val());
        if (qty > 0) {
            cart[index].quantity = qty;
            updateCart();
        }
    });

    $(document).on('click', '.remove-item', function() {
        const index = $(this).data('index');
        cart.splice(index, 1);
        updateCart();
    });

    $('#clearCartBtn').on('click', function() {
        if (confirm('Clear all items from cart?')) {
            cart = [];
            updateCart();
        }
    });

    $('#customerGivenAmount').on('input', function() {
        const given = parseFloat($(this).val()) || 0;
        const total = parseFloat($('#totalDisplay').text().replace('Rs. ', '').replace(',', '')) || 0;
        const change = given - total;
        $('#changeAmount').text('Rs. ' + change.toFixed(2));
    });

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

        const subtotal = parseFloat($('#subtotalDisplay').text().replace('Rs. ', '').replace(',', '')) || 0;
        const discount = parseFloat($('#discountDisplay').text().replace('Rs. ', '').replace(',', '')) || 0;
        const tax = parseFloat($('#taxDisplay').text().replace('Rs. ', '').replace(',', '')) || 0;
        const total = parseFloat($('#totalDisplay').text().replace('Rs. ', '').replace(',', '')) || 0;

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
                    if (barcodeMode) {
                        const scan = document.getElementById('posBarcodeScan');
                        if (scan) { scan.focus(); scan.select(); }
                    }
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to create sale'));
            }
        });
    });

    // POS_MT Barcode scan behaviour
    const barcodeScanInput = document.getElementById('posBarcodeScan');
    if (barcodeScanInput) {
        const productsCatalog = document.getElementById('productsCatalog');
        let scanIdleTimer = null;
        let lastScanKeyAt = 0;
        let rapidKeyCount = 0;
        let scanBusy = false;
        let lastAcceptedCode = '';
        let lastAcceptedAt = 0;
        const SCAN_IDLE_MS = 120;
        const RAPID_KEY_MS = 55;
        const SAME_CODE_COOLDOWN_MS = 2000;

        function normalizeScanCode(rawCode) {
            return String(rawCode || '')
                .replace(/[\u0000-\u001F\u007F]/g, '')
                .replace(/^\*+|\*+$/g, '')
                .trim();
        }

        function findProductRowByScanCode(rawCode) {
            const code = normalizeScanCode(rawCode);
            if (!code || !productsCatalog) return null;
            const lower = code.toLowerCase();
            const rows = Array.from(productsCatalog.querySelectorAll('.pos-product-row'));
            let match = rows.find(function (row) {
                return (row.getAttribute('data-barcode') || '').trim().toLowerCase() === lower;
            });
            if (match) return match;
            match = rows.find(function (row) {
                return (row.getAttribute('data-code') || '').trim().toLowerCase() === lower;
            });
            return match || null;
        }

        function addProductFromBarcode(rawCode) {
            const code = normalizeScanCode(rawCode);
            const row = findProductRowByScanCode(code);
            if (!row) {
                showAlert('warning', 'No product found for "' + code + '".');
                return false;
            }
            addProductToCart({
                uid: row.getAttribute('data-uid'),
                name: row.getAttribute('data-name'),
                price: row.getAttribute('data-price'),
                tax_percent: row.getAttribute('data-tax'),
                discount_percent: row.getAttribute('data-discount')
            });
            showAlert('success', 'Added: ' + (row.getAttribute('data-name') || code));
            return true;
        }

        function focusBarcodeScan() {
            window.setTimeout(function () {
                if (document.activeElement === barcodeScanInput) {
                    barcodeScanInput.select();
                    return;
                }
                const active = document.activeElement;
                if (active && active !== document.body && active !== document.documentElement) {
                    const tag = (active.tagName || '').toLowerCase();
                    if (tag === 'input' || tag === 'textarea' || tag === 'select' || active.isContentEditable) {
                        if (active !== barcodeScanInput) return;
                    }
                }
                barcodeScanInput.focus();
                barcodeScanInput.select();
            }, 0);
        }

        function commitBarcodeScan() {
            if (scanBusy) return;
            if (scanIdleTimer) {
                clearTimeout(scanIdleTimer);
                scanIdleTimer = null;
            }
            rapidKeyCount = 0;
            lastScanKeyAt = 0;
            const code = normalizeScanCode(barcodeScanInput.value);
            barcodeScanInput.value = '';
            if (!code) {
                focusBarcodeScan();
                return;
            }
            const now = Date.now();
            if (code.toLowerCase() === lastAcceptedCode && (now - lastAcceptedAt) < SAME_CODE_COOLDOWN_MS) {
                focusBarcodeScan();
                return;
            }
            scanBusy = true;
            try {
                const added = addProductFromBarcode(code);
                if (added) {
                    lastAcceptedCode = code.toLowerCase();
                    lastAcceptedAt = now;
                }
            } finally {
                window.setTimeout(function () { scanBusy = false; }, 250);
            }
            focusBarcodeScan();
        }

        function noteScanKey() {
            const now = Date.now();
            if (lastScanKeyAt && (now - lastScanKeyAt) <= RAPID_KEY_MS) {
                rapidKeyCount += 1;
            } else {
                rapidKeyCount = 1;
            }
            lastScanKeyAt = now;
            if (scanIdleTimer) clearTimeout(scanIdleTimer);
            scanIdleTimer = setTimeout(function () {
                scanIdleTimer = null;
                const code = normalizeScanCode(barcodeScanInput.value);
                if (rapidKeyCount >= 4 && code.length >= 3) {
                    commitBarcodeScan();
                }
            }, SCAN_IDLE_MS);
        }

        barcodeScanInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                commitBarcodeScan();
                return;
            }
            if (e.key.length === 1) {
                noteScanKey();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey || e.altKey || e.metaKey) return;
            const active = document.activeElement;
            if (active === barcodeScanInput) return;
            if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.tagName === 'SELECT' || active.isContentEditable)) {
                return;
            }
            if (e.key === 'Enter' || e.key === 'Tab') {
                if (normalizeScanCode(barcodeScanInput.value)) {
                    e.preventDefault();
                    commitBarcodeScan();
                }
                return;
            }
            if (e.key.length !== 1) return;
            e.preventDefault();
            barcodeScanInput.focus();
            barcodeScanInput.value = (barcodeScanInput.value || '') + e.key;
            noteScanKey();
        }, true);

        document.getElementById('posSaleLayout')?.addEventListener('mouseup', function (e) {
            if (e.target.closest('input, select, textarea, button, a')) return;
            focusBarcodeScan();
        });

        focusBarcodeScan();
    }
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
