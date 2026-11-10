@extends('layouts.app')

@section('title', $pageTitle ?? 'POS - Cash Sale')

@section('content')
@php
    $barcodeMode = !empty($enableBarcodeScan);

    $itemIcon = function (?string $name): string {
        $n = strtolower($name ?? '');
        if (str_contains($n, 'milk') || str_contains($n, 'dairy') || str_contains($n, 'yogurt') || str_contains($n, 'yoghurt') || str_contains($n, 'cream')) return 'bi-cup-straw';
        if (str_contains($n, 'butter') || str_contains($n, 'ghee') || str_contains($n, 'margarine')) return 'bi-droplet-fill';
        if (str_contains($n, 'cheese') || str_contains($n, 'topping')) return 'bi-circle-half';
        if (str_contains($n, 'bread') || str_contains($n, 'roti') || str_contains($n, 'bun') || str_contains($n, 'loaf') || str_contains($n, 'rusk')) return 'bi-basket2-fill';
        if (str_contains($n, 'egg')) return 'bi-egg-fill';
        if (str_contains($n, 'rice') || str_contains($n, 'flour') || str_contains($n, 'atta') || str_contains($n, 'grocery') || str_contains($n, 'pulse') || str_contains($n, 'dal')) return 'bi-bag-fill';
        if (str_contains($n, 'oil') || str_contains($n, 'spice') || str_contains($n, 'salt') || str_contains($n, 'sugar') || str_contains($n, 'extra')) return 'bi-box-seam-fill';
        if (str_contains($n, 'fruit') || str_contains($n, 'apple') || str_contains($n, 'banana') || str_contains($n, 'mango') || str_contains($n, 'orange')) return 'bi-apple';
        if (str_contains($n, 'veg') || str_contains($n, 'salad') || str_contains($n, 'tomato') || str_contains($n, 'onion')) return 'bi-flower1';
        if (str_contains($n, 'meat') || str_contains($n, 'mutton') || str_contains($n, 'beef') || str_contains($n, 'bbq') || str_contains($n, 'barbe')) return 'bi-fire';
        if (str_contains($n, 'chicken') || str_contains($n, 'broast') || str_contains($n, 'crispy')) return 'bi-egg-fill';
        if (str_contains($n, 'fish') || str_contains($n, 'seafood')) return 'bi-water';
        if (str_contains($n, 'pizza') || str_contains($n, 'calzone')) return 'bi-pie-chart-fill';
        if (str_contains($n, 'burger') || str_contains($n, 'sandwich')) return 'bi-layers-fill';
        if (str_contains($n, 'fries') || str_contains($n, 'fry') || str_contains($n, 'chips')) return 'bi-grid-3x2-gap-fill';
        if (str_contains($n, 'pasta') || str_contains($n, 'lasagna') || str_contains($n, 'noodle')) return 'bi-intersect';
        if (str_contains($n, 'roll') || str_contains($n, 'wrap')) return 'bi-receipt';
        if (str_contains($n, 'drink') || str_contains($n, 'juice') || str_contains($n, 'beverage') || str_contains($n, 'soda') || str_contains($n, 'water') || str_contains($n, 'tea') || str_contains($n, 'coffee')) return 'bi-cup-hot-fill';
        if (str_contains($n, 'ice') || str_contains($n, 'dessert') || str_contains($n, 'sweet') || str_contains($n, 'cake')) return 'bi-snow2';
        if (str_contains($n, 'bag')) return 'bi-bag-fill';
        if (str_contains($n, 'deal') || str_contains($n, 'combo') || str_contains($n, 'offer')) return 'bi-tags-fill';
        if (str_contains($n, 'deliver')) return 'bi-truck';
        if (str_contains($n, 'dawn') || str_contains($n, 'brand')) return 'bi-buildings-fill';
        return 'bi-box2-fill';
    };

    $iconTone = function (?string $name): string {
        $tones = ['blue', 'orange', 'green', 'pink', 'teal', 'violet', 'amber', 'rose'];
        $key = $name ?? '';
        $hash = 0;
        $len = strlen($key);
        for ($i = 0; $i < $len; $i++) {
            $hash = ($hash * 31 + ord($key[$i])) & 0x7fffffff;
        }
        return $tones[$hash % count($tones)];
    };

    $cardPrice = function ($price): string {
        $price = (float) $price;
        return fmod($price, 1.0) == 0.0
            ? 'Rs: ' . number_format($price, 0)
            : 'Rs: ' . number_format($price, 2);
    };
@endphp
<div class="container-fluid">
    <div id="posAlert" class="alert d-none" role="alert"></div>

    <div class="pos-sale-split pos-sale-layout {{ $barcodeMode ? 'pos-sale-layout--barcode-only' : 'pos-sale-layout--cards' }}" id="posSaleLayout">
        <!-- Product / Barcode Selection -->
        <div class="pos-sale-pane pos-sale-pane--items {{ $barcodeMode ? 'pos-sale-panel--barcode-only' : '' }}" id="posItemsPane">
            <div class="card h-100">
                <div class="card-body {{ $barcodeMode ? '' : 'pos-cash-catalog-body' }}">
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
                                 data-discount="{{ $product->discount_percent }}"
                                 data-stock="{{ ($stockByProduct ?? [])[$product->uid] ?? 0 }}"></div>
                            @endforeach
                        </div>
                        @if($products->isEmpty())
                            <div class="p-3 text-muted">No active products found.</div>
                        @endif
                    @else
                        <div class="mb-2">
                            <input type="text" id="productSearch" class="form-control form-control-sm"
                                   placeholder="Search product by name, code, or barcode...">
                        </div>

                        @php $catTone = 0; @endphp
                        <div class="pos-category-tiles mb-2" id="productCategories" role="group" aria-label="Product categories">
                            <button type="button"
                                    class="btn btn-sm pos-category-btn pos-cat-tone-{{ $catTone++ % 12 }} active category-btn"
                                    data-category="">
                                <span class="pos-cat-accent" aria-hidden="true"></span>
                                <span class="pos-cat-icon" aria-hidden="true"><i class="bi bi-grid-fill"></i></span>
                                <span class="pos-cat-label">All Items</span>
                            </button>
                            @foreach($categories as $category)
                            <button type="button"
                                    class="btn btn-sm pos-category-btn pos-cat-tone-{{ $catTone++ % 12 }} category-btn"
                                    data-category="{{ $category->uid }}">
                                <span class="pos-cat-accent" aria-hidden="true"></span>
                                <span class="pos-cat-icon" aria-hidden="true"><i class="bi {{ $itemIcon($category->category_name) }}"></i></span>
                                <span class="pos-cat-label">{{ $category->category_name }}</span>
                            </button>
                            @endforeach
                        </div>

                        <div class="pos-product-cards" id="productsGrid">
                            @foreach($products as $product)
                                @php
                                    $iconSource = trim(($product->product_name ?? '') . ' ' . (optional($product->category)->category_name ?? ''));
                                @endphp
                                <button type="button"
                                        class="pos-item-card product-item add-product"
                                        data-category="{{ $product->category_uid }}"
                                        data-uid="{{ $product->uid }}"
                                        data-name="{{ $product->product_name }}"
                                        data-barcode="{{ $product->barcode }}"
                                        data-code="{{ $product->product_code }}"
                                        data-price="{{ $product->sale_price }}"
                                        data-tax="{{ $product->tax_percent }}"
                                        data-discount="{{ $product->discount_percent }}"
                                        data-stock="{{ ($stockByProduct ?? [])[$product->uid] ?? 0 }}"
                                        title="{{ $product->product_code }}">
                                    @if(!empty($product->image_path))
                                        <img class="pos-item-card-photo" src="{{ $product->image_path }}" alt="">
                                    @else
                                        <span class="pos-icon-tile pos-icon-tile--lg pos-icon-tone-{{ $iconTone($iconSource) }}" aria-hidden="true">
                                            <i class="bi {{ $itemIcon($iconSource) }}"></i>
                                        </span>
                                    @endif
                                    <span class="pos-item-card-name">{{ $product->product_name }}</span>
                                    <span class="pos-item-card-price">{{ $cardPrice($product->sale_price) }}</span>
                                </button>
                            @endforeach
                        </div>
                        @if($products->isEmpty())
                            <div class="p-3 text-muted">No active products found.</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="pos-sale-splitter"
             id="posSaleSplitter"
             role="separator"
             aria-orientation="vertical"
             aria-label="Resize items and basket"
             tabindex="0"
             title="Drag to resize"></div>

        <!-- Cart Section -->
        <div class="pos-sale-pane pos-sale-pane--basket" id="posBasketPane">
            <div class="card h-100">
                <div class="card-body pos-basket-body">
                    <input type="hidden" id="cashPaymentMethodUid" value="{{ $cashPaymentMethodUid }}">

                    <div class="pos-basket-actions">
                        <button type="button" id="completeSaleBtn" class="btn btn-success btn-sm pos-basket-action-btn">
                            <i class="bi bi-check-circle"></i> Complete Sale
                        </button>
                        <button type="button" id="clearCartBtn" class="btn btn-outline-danger btn-sm pos-basket-action-btn">
                            <i class="bi bi-x-circle"></i> Clear Cart
                        </button>
                    </div>

                    <hr class="pos-basket-divider">

                    <div class="table-responsive pos-basket-table-wrap">
                        <table class="table table-sm align-middle mb-0" id="cartTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="110" class="text-center">Qty</th>
                                    <th width="72" class="text-end">Total</th>
                                    <th width="28"></th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                                <tr id="emptyCart">
                                    <td colspan="4" class="text-center text-muted py-2">Cart is empty</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="pos-basket-divider">

                    <hr class="pos-basket-divider">
                    <div class="pos-basket-totals">
                        <div class="pos-basket-total-row">
                            <strong>Subtotal:</strong>
                            <span id="subtotalDisplay">Rs. 0.00</span>
                        </div>
                        <div class="pos-basket-total-row">
                            <strong>Tax:</strong>
                            <span id="taxDisplay">Rs. 0.00</span>
                        </div>
                        <hr class="pos-basket-divider pos-basket-divider--totals">
                        <div class="pos-basket-total-row pos-basket-total-row--net">
                            <strong>Total:</strong>
                            <strong id="totalDisplay">Rs. 0.00</strong>
                        </div>
                    </div>
                    <hr class="pos-basket-divider">

                    <div class="pos-pay-fields">
                        <div class="pos-pay-row">
                            <label class="pos-pay-label" for="discountAmount">Discount</label>
                            <input type="number" id="discountAmount" class="form-control form-control-sm bg-light text-end pos-pay-input" value="0.00"
                                   step="0.01" min="0" readonly tabindex="-1" aria-readonly="true">
                            <span id="discountDisplay" class="d-none">Rs. 0.00</span>
                        </div>
                        <div class="pos-pay-row">
                            <label class="pos-pay-label" for="customerGivenAmount">Customer Given</label>
                            <input type="number" id="customerGivenAmount" class="form-control form-control-sm text-end pos-pay-input"
                                   placeholder="0.00" step="0.01" min="0">
                        </div>
                        <div class="pos-pay-row">
                            <label class="pos-pay-label" for="changeAmountInput">Change</label>
                            <input type="text" id="changeAmountInput" class="form-control form-control-sm bg-light text-end pos-pay-input" value="Rs. 0.00"
                                   readonly tabindex="-1" aria-readonly="true">
                            <span id="changeAmount" class="d-none">Rs. 0.00</span>
                        </div>
                        <div class="pos-remaining-qty" id="remainingQtyBox" aria-live="polite">
                            <div class="pos-remaining-qty-title">Quantity Remaining</div>
                            <div id="remainingQtyList" class="pos-remaining-qty-list">
                                <div class="pos-remaining-qty-empty text-muted">Add items to see remaining stock</div>
                            </div>
                        </div>
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
const cashPaymentMethodUid = @json($cashPaymentMethodUid);

function showAlert(type, message) {
    const el = document.getElementById('posAlert');
    if (!el) { alert(message); return; }
    el.className = 'alert alert-' + type;
    el.textContent = message;
    el.classList.remove('d-none');
    window.setTimeout(function () { el.classList.add('d-none'); }, 2500);
}

function formatStockQty(n) {
    const v = Number(n);
    if (!isFinite(v)) return '0';
    if (Math.abs(v - Math.round(v)) < 0.0005) return String(Math.round(v));
    return (Math.round(v * 1000) / 1000).toFixed(3).replace(/\.?0+$/, '');
}

function escapeHtml(text) {
    return String(text == null ? '' : text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function lookupProductStock(uid) {
    var nodes = document.querySelectorAll('[data-uid="' + String(uid) + '"]');
    for (var i = 0; i < nodes.length; i++) {
        if (nodes[i].hasAttribute('data-stock')) {
            var stock = parseFloat(nodes[i].getAttribute('data-stock'));
            if (isFinite(stock)) return stock;
        }
    }
    return 0;
}

function resolveItemStock(item) {
    var raw = item && item.stock;
    var stock = parseFloat(raw);
    if (isFinite(stock)) return stock;
    return lookupProductStock(item && item.uid);
}

function updateRemainingQtyDisplay() {
    var listEl = document.getElementById('remainingQtyList');
    if (!listEl) return;

    if (!cart || !cart.length) {
        listEl.innerHTML = '<div class="pos-remaining-qty-empty text-muted">Add items to see remaining stock</div>';
        return;
    }

    var html = '';
    for (var i = 0; i < cart.length; i++) {
        var item = cart[i];
        var stock = resolveItemStock(item);
        var inCart = parseFloat(item.quantity);
        if (!isFinite(inCart)) inCart = 0;
        var remaining = stock - inCart;
        var qtyClass = remaining < 0 ? 'is-negative' : (remaining <= 5 ? 'is-low' : 'is-ok');
        var label = String(item.name || 'Item') + ' ' + formatCartPrice(item.price);
        html += '<div class="pos-remaining-qty-line ' + qtyClass + '">'
            + '<span class="pos-remaining-qty-name">' + escapeHtml(label) + '</span>'
            + '<strong class="pos-remaining-qty-left">' + formatStockQty(remaining) + ' left</strong>'
            + '</div>';
    }
    listEl.innerHTML = html;
}

function addProductToCart(product) {
    var stockAttr = product && product.stock;
    var stock = parseFloat(stockAttr);
    if (!isFinite(stock)) {
        stock = lookupProductStock(product && product.uid);
    }
    var existingIndex = cart.findIndex(function (item) {
        return String(item.uid) === String(product.uid);
    });
    if (existingIndex >= 0) {
        cart[existingIndex].quantity++;
        cart[existingIndex].stock = stock;
    } else {
        cart.push({
            uid: product.uid,
            name: product.name,
            price: parseFloat(product.price) || 0,
            tax_percent: parseFloat(product.tax_percent) || 0,
            discount_percent: parseFloat(product.discount_percent) || 0,
            stock: stock,
            quantity: 1
        });
    }
    updateCart();
}

function initPosSaleSplitter() {
    const layout = document.getElementById('posSaleLayout');
    const itemsPane = document.getElementById('posItemsPane');
    const basketPane = document.getElementById('posBasketPane');
    const splitter = document.getElementById('posSaleSplitter');
    if (!layout || !itemsPane || !basketPane || !splitter) return;

    const STORAGE_KEY = 'posCashSplitPct';
    const MIN_PCT = 28;
    const MAX_PCT = 78;

    function applySplit(itemsPct) {
        const pct = Math.min(MAX_PCT, Math.max(MIN_PCT, Number(itemsPct) || 58));
        itemsPane.style.flex = '0 0 ' + pct + '%';
        basketPane.style.flex = '1 1 0';
        layout.style.setProperty('--pos-split-items', pct + '%');
        splitter.setAttribute('aria-valuenow', String(Math.round(pct)));
        try { localStorage.setItem(STORAGE_KEY, String(pct)); } catch (e) {}
    }

    const saved = parseFloat(localStorage.getItem(STORAGE_KEY) || '');
    applySplit(Number.isFinite(saved) ? saved : 58);

    let dragging = false;

    function splitFromClientX(clientX) {
        const rect = layout.getBoundingClientRect();
        if (rect.width <= 0) return;
        const splitterWidth = splitter.offsetWidth || 8;
        const pct = ((clientX - rect.left - splitterWidth / 2) / rect.width) * 100;
        applySplit(pct);
    }

    splitter.addEventListener('pointerdown', function (e) {
        if (window.matchMedia('(max-width: 767.98px)').matches) return;
        dragging = true;
        splitter.setPointerCapture(e.pointerId);
        layout.classList.add('is-resizing');
        e.preventDefault();
    });

    splitter.addEventListener('pointermove', function (e) {
        if (!dragging) return;
        splitFromClientX(e.clientX);
    });

    function endDrag(e) {
        if (!dragging) return;
        dragging = false;
        layout.classList.remove('is-resizing');
        try { splitter.releasePointerCapture(e.pointerId); } catch (err) {}
    }

    splitter.addEventListener('pointerup', endDrag);
    splitter.addEventListener('pointercancel', endDrag);

    splitter.addEventListener('keydown', function (e) {
        const current = parseFloat(localStorage.getItem(STORAGE_KEY) || '58') || 58;
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            applySplit(current - 2);
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            applySplit(current + 2);
        } else if (e.key === 'Home') {
            e.preventDefault();
            applySplit(58);
        }
    });

    splitter.setAttribute('aria-valuemin', String(MIN_PCT));
    splitter.setAttribute('aria-valuemax', String(MAX_PCT));
}

$(document).ready(function() {
    initPosSaleSplitter();

    if (!barcodeMode) {
        $('#productSearch').on('keyup', function() {
            const search = $(this).val().toLowerCase();
            const activeCategory = String($('.category-btn.active').data('category') || '');
            $('.product-item').each(function() {
                const name = String($(this).data('name') || '').toLowerCase();
                const code = String($(this).data('code') || '').toLowerCase();
                const barcode = String($(this).data('barcode') || '').toLowerCase();
                const category = String($(this).data('category') || '');
                const matchesSearch = !search || name.includes(search) || code.includes(search) || barcode.includes(search);
                const matchesCategory = !activeCategory || category === activeCategory;
                $(this).toggle(matchesSearch && matchesCategory);
            });
        });

        $('.category-btn').on('click', function() {
            const categoryUid = String($(this).data('category') || '');
            $('.category-btn').removeClass('active');
            $(this).addClass('active');

            const search = String($('#productSearch').val() || '').toLowerCase();
            $('.product-item').each(function() {
                const name = String($(this).data('name') || '').toLowerCase();
                const code = String($(this).data('code') || '').toLowerCase();
                const barcode = String($(this).data('barcode') || '').toLowerCase();
                const category = String($(this).data('category') || '');
                const matchesSearch = !search || name.includes(search) || code.includes(search) || barcode.includes(search);
                const matchesCategory = !categoryUid || category === categoryUid;
                $(this).toggle(matchesSearch && matchesCategory);
            });
        });

        $(document).on('click', '.add-product', function() {
            var el = this;
            addProductToCart({
                uid: el.getAttribute('data-uid'),
                name: el.getAttribute('data-name'),
                price: el.getAttribute('data-price'),
                tax_percent: el.getAttribute('data-tax'),
                discount_percent: el.getAttribute('data-discount'),
                stock: el.getAttribute('data-stock')
            });
        });
    }

    $(document).on('change', '.qty-input', function() {
        const index = $(this).data('index');
        const qty = parseFloat($(this).val());
        if (qty > 0) {
            cart[index].quantity = qty;
            updateCart();
        } else {
            $(this).val(cart[index].quantity);
        }
    });

    $(document).on('click', '.qty-minus', function() {
        const index = $(this).data('index');
        if (!cart[index]) return;
        if (cart[index].quantity <= 1) {
            cart.splice(index, 1);
        } else {
            cart[index].quantity -= 1;
        }
        updateCart();
    });

    $(document).on('click', '.qty-plus', function() {
        const index = $(this).data('index');
        if (!cart[index]) return;
        cart[index].quantity += 1;
        updateCart();
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

    $('#customerGivenAmount').on('input', refreshChangeAmount);

    $('#completeSaleBtn').on('click', function() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        if (!cashPaymentMethodUid) {
            alert('Cash payment method is not configured.');
            return;
        }

        const subtotal = parseFloat($('#subtotalDisplay').text().replace('Rs. ', '').replace(',', '')) || 0;
        const discount = parseFloat($('#discountAmount').val()) || parseFloat(String($('#discountDisplay').text() || '').replace(/Rs\.\s*/i, '').replace(/,/g, '')) || 0;
        const tax = parseFloat($('#taxDisplay').text().replace('Rs. ', '').replace(',', '')) || 0;
        const total = parseFloat($('#totalDisplay').text().replace('Rs. ', '').replace(',', '')) || 0;

        const saleData = {
            customer_uid: null,
            payment_method_uid: cashPaymentMethodUid,
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
                    $('#customerGivenAmount').val('');
                    $('#changeAmount').text('Rs. 0.00');
                    $('#changeAmountInput').val('Rs. 0.00');
                    $('#discountAmount').val('0.00');
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
                discount_percent: row.getAttribute('data-discount'),
                stock: row.getAttribute('data-stock')
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

function formatCartPrice(price) {
    const n = Number(price) || 0;
    return Number.isInteger(n) ? String(n) : n.toFixed(2);
}

function refreshChangeAmount() {
    const given = parseFloat($('#customerGivenAmount').val()) || 0;
    const totalText = String($('#totalDisplay').text() || '').replace(/Rs\.\s*/i, '').replace(/,/g, '');
    const total = parseFloat(totalText) || 0;
    const change = given - total;
    const label = 'Rs. ' + change.toFixed(2);
    $('#changeAmount').text(label);
    $('#changeAmountInput').val(label);
}

function updateCart() {
    const tbody = $('#cartItems');
    tbody.empty();

    if (cart.length === 0) {
        tbody.append('<tr id="emptyCart"><td colspan="4" class="text-center text-muted">Cart is empty</td></tr>');
        $('#subtotalDisplay').text('Rs. 0.00');
        $('#discountDisplay').text('Rs. 0.00');
        $('#discountAmount').val('0.00');
        $('#taxDisplay').text('Rs. 0.00');
        $('#totalDisplay').text('Rs. 0.00');
        refreshChangeAmount();
        updateRemainingQtyDisplay();
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

        const label = `${item.name} ${formatCartPrice(item.price)}`;
        const row = `
            <tr>
                <td><span class="pos-cart-item-name">${label}</span></td>
                <td>
                    <div class="pos-qty-stepper">
                        <button type="button" class="btn btn-sm btn-outline-secondary qty-minus" data-index="${index}" aria-label="Decrease quantity">−</button>
                        <input type="number" class="form-control form-control-sm qty-input" value="${item.quantity}"
                               min="1" step="1" data-index="${index}">
                        <button type="button" class="btn btn-sm btn-outline-secondary qty-plus" data-index="${index}" aria-label="Increase quantity">+</button>
                    </div>
                </td>
                <td class="text-end"><small>Rs. ${itemTotal.toFixed(2)}</small></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-item" data-index="${index}">×</button></td>
            </tr>
        `;
        tbody.append(row);
    });

    const netTotal = subtotal - totalDiscount + totalTax;

    $('#subtotalDisplay').text('Rs. ' + subtotal.toFixed(2));
    $('#discountDisplay').text('Rs. ' + totalDiscount.toFixed(2));
    $('#discountAmount').val(totalDiscount.toFixed(2));
    $('#taxDisplay').text('Rs. ' + totalTax.toFixed(2));
    $('#totalDisplay').text('Rs. ' + netTotal.toFixed(2));
    refreshChangeAmount();
    updateRemainingQtyDisplay();
}
</script>
@endpush
