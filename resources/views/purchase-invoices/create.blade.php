@extends('layouts.app')

@section('title', 'Create Purchase Invoice')

@section('content')
@php
    $maxLines = 5;
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading mb-1">Create Purchase Invoice</h1>
            <p class="text-muted mb-0">Add up to {{ $maxLines }} items. Quantity increases inventory stock.</p>
        </div>
        <div class="text-muted small">Invoice No: <strong>{{ $invoiceNo }}</strong></div>
    </div>

    <div id="purchaseAlert" class="alert d-none" role="alert"></div>

    @if($errors->any())
        <div class="alert alert-danger border-danger" id="purchaseValidationErrors">
            <strong><i class="bi bi-x-circle"></i> Purchase was not saved. Fix these issues:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purchase-invoices.store') }}" id="purchaseForm" novalidate>
        @csrf

        <div class="bg-white border rounded p-3 mb-3">
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="purchase_date">Purchase Date *</label>
                    <div class="invoice-meta-field">
                        <input id="purchase_date" type="date" name="purchase_date"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <label class="invoice-meta-title" for="vendor_uid">Vendor *</label>
                    <div class="invoice-meta-field">
                        <select id="vendor_uid" name="vendor_uid" class="form-select form-select-sm invoice-meta-input {{ $errors->has('vendor_uid') ? 'is-invalid' : '' }}" required>
                            <option value="">Select vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->uid }}" @selected(old('vendor_uid') == $vendor->uid)>
                                    {{ $vendor->vendor_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="branch_uid">Branch *</label>
                    <div class="invoice-meta-field">
                        <select id="branch_uid" name="branch_uid" class="form-select form-select-sm invoice-meta-input {{ $errors->has('branch_uid') ? 'is-invalid' : '' }}" required>
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->uid }}" @selected((string) old('branch_uid', $defaultBranchUid) === (string) $branch->uid)>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_uid')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="vendor_invoice_no">Vendor Invoice No</label>
                    <div class="invoice-meta-field">
                        <input id="vendor_invoice_no" type="text" name="vendor_invoice_no"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('vendor_invoice_no') }}">
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="remarks">Remarks</label>
                    <div class="invoice-meta-field">
                        <input id="remarks" type="text" name="remarks"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('remarks') }}">
                    </div>
                    <label class="invoice-meta-title" for="paid_amount">Paid Amount</label>
                    <div class="invoice-meta-field">
                        <input id="paid_amount" type="number" step="0.01" min="0" name="paid_amount"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('paid_amount', '0') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <strong>Receive into inventory (max {{ $maxLines }} items)</strong>
                <span class="badge text-bg-primary" id="itemsAddedBadge">0 / {{ $maxLines }} items added</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="purchaseItemsTable">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Product *</th>
                                <th style="width:120px" class="text-end">Qty *</th>
                                <th style="width:140px" class="text-end">Unit Cost *</th>
                                <th style="width:140px" class="text-end">Line Total</th>
                                <th style="width:100px">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < $maxLines; $i++)
                                @php
                                    $oldProduct = old("items.$i.product_uid");
                                    $oldQty = old("items.$i.quantity");
                                    $oldPrice = old("items.$i.unit_price");
                                @endphp
                                <tr class="purchase-line" data-index="{{ $i }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <select name="items[{{ $i }}][product_uid]"
                                                class="form-select form-select-sm item-product {{ $errors->has("items.$i.product_uid") ? 'is-invalid' : '' }}"
                                                data-index="{{ $i }}">
                                            <option value="">Select product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->uid }}"
                                                        data-price="{{ $product->purchase_price }}"
                                                        data-name="{{ $product->product_name }}"
                                                        @selected($oldProduct == $product->uid)>
                                                    {{ $product->product_name }} ({{ $product->product_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("items.$i.product_uid")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </td>
                                    <td>
                                        <input type="number" step="0.001" min="0" name="items[{{ $i }}][quantity]"
                                               class="form-control form-control-sm text-end item-qty {{ $errors->has("items.$i.quantity") ? 'is-invalid' : '' }}"
                                               value="{{ $oldQty }}" placeholder="0">
                                        @error("items.$i.quantity")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]"
                                               class="form-control form-control-sm text-end item-price {{ $errors->has("items.$i.unit_price") ? 'is-invalid' : '' }}"
                                               value="{{ $oldPrice }}" placeholder="0.00">
                                        @error("items.$i.unit_price")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </td>
                                    <td class="text-end fw-semibold item-line-total">0.00</td>
                                    <td>
                                        <span class="badge text-bg-secondary item-status">Empty</span>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <div class="text-muted small mb-1">Items ready to save</div>
                    <ul class="list-group" id="addedItemsSummary">
                        <li class="list-group-item text-muted" id="addedItemsEmpty">No products added yet. Select a product in a row above.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-3 mb-3">
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sub Total</span>
                        <strong id="displaySubTotal">0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Net Amount</span>
                        <strong id="displayNetAmount">0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Items Count</span>
                        <strong id="displayItemsCount">0</strong>
                    </div>
                </div>
            </div>
            <input type="hidden" name="sub_total" id="sub_total" value="0">
            <input type="hidden" name="discount_amount" value="0">
            <input type="hidden" name="tax_amount" value="0">
            <input type="hidden" name="net_amount" id="net_amount" value="0">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-lg" id="savePurchaseBtn">
                <i class="bi bi-check-circle"></i> Save Purchase
            </button>
            <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .purchase-line.is-added {
        background: #ecfdf5;
    }
    .purchase-line.is-incomplete {
        background: #fff7ed;
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    var maxLines = {{ $maxLines }};
    var alertTimer = null;

    function toNumber(value) {
        var n = parseFloat(value);
        return isNaN(n) ? 0 : n;
    }

    function formatMoney(n) {
        return toNumber(n).toFixed(2);
    }

    function showAlert(type, message) {
        var el = document.getElementById('purchaseAlert');
        if (!el) return;
        el.className = 'alert alert-' + type;
        el.textContent = message;
        el.classList.remove('d-none');
        if (alertTimer) clearTimeout(alertTimer);
        alertTimer = setTimeout(function () {
            el.classList.add('d-none');
        }, 3500);
    }

    function getLineState(row) {
        var product = row.querySelector('.item-product');
        var qty = toNumber(row.querySelector('.item-qty').value);
        var price = toNumber(row.querySelector('.item-price').value);
        var option = product.options[product.selectedIndex];
        var name = option && product.value ? (option.getAttribute('data-name') || option.textContent.trim()) : '';
        var hasProduct = !!product.value;
        var isComplete = hasProduct && qty > 0 && price >= 0;
        var isIncomplete = hasProduct && qty <= 0;

        return {
            hasProduct: hasProduct,
            isComplete: isComplete,
            isIncomplete: isIncomplete,
            name: name,
            qty: qty,
            price: price,
            lineTotal: isComplete ? qty * price : 0
        };
    }

    function refreshSummary() {
        var summary = document.getElementById('addedItemsSummary');
        var empty = document.getElementById('addedItemsEmpty');
        var badge = document.getElementById('itemsAddedBadge');
        var countEl = document.getElementById('displayItemsCount');
        var completeCount = 0;

        summary.querySelectorAll('.added-item-row').forEach(function (n) { n.remove(); });

        document.querySelectorAll('.purchase-line').forEach(function (row, index) {
            var state = getLineState(row);
            var status = row.querySelector('.item-status');
            row.classList.toggle('is-added', state.isComplete);
            row.classList.toggle('is-incomplete', state.isIncomplete);

            if (state.isComplete) {
                completeCount++;
                status.className = 'badge text-bg-success item-status';
                status.textContent = 'Added';

                var li = document.createElement('li');
                li.className = 'list-group-item added-item-row d-flex justify-content-between align-items-center';
                li.innerHTML =
                    '<span><strong>#' + (index + 1) + '</strong> ' + state.name +
                    ' <span class="text-muted">× ' + state.qty + '</span></span>' +
                    '<span class="fw-semibold">Rs. ' + formatMoney(state.lineTotal) + '</span>';
                summary.appendChild(li);
            } else if (state.isIncomplete) {
                status.className = 'badge text-bg-warning item-status';
                status.textContent = 'Need Qty';
            } else if (state.hasProduct) {
                status.className = 'badge text-bg-warning item-status';
                status.textContent = 'Incomplete';
            } else {
                status.className = 'badge text-bg-secondary item-status';
                status.textContent = 'Empty';
            }
        });

        if (empty) {
            empty.classList.toggle('d-none', completeCount > 0);
        }

        badge.textContent = completeCount + ' / ' + maxLines + ' items added';
        badge.className = completeCount > 0 ? 'badge text-bg-success' : 'badge text-bg-primary';
        countEl.textContent = String(completeCount);
        return completeCount;
    }

    function recalc(options) {
        options = options || {};
        var subTotal = 0;

        document.querySelectorAll('.purchase-line').forEach(function (row) {
            var state = getLineState(row);
            row.querySelector('.item-line-total').textContent = formatMoney(state.lineTotal);
            subTotal += state.lineTotal;
        });

        document.getElementById('displaySubTotal').textContent = formatMoney(subTotal);
        document.getElementById('displayNetAmount').textContent = formatMoney(subTotal);
        document.getElementById('sub_total').value = formatMoney(subTotal);
        document.getElementById('net_amount').value = formatMoney(subTotal);

        var completeCount = refreshSummary();

        if (options.announceProduct) {
            if (options.announceProduct.complete) {
                showAlert('success', 'Added: ' + options.announceProduct.name + ' (Qty ' + options.announceProduct.qty + '). Items ready: ' + completeCount + '/' + maxLines + '.');
            } else if (options.announceProduct.cleared) {
                showAlert('secondary', 'Product removed from line #' + options.announceProduct.lineNo + '.');
            } else if (options.announceProduct.incomplete) {
                showAlert('warning', 'Selected ' + options.announceProduct.name + '. Enter quantity to mark this item as added.');
            }
        }
    }

    document.querySelectorAll('.item-product').forEach(function (select) {
        select.addEventListener('change', function () {
            var row = select.closest('.purchase-line');
            var option = select.options[select.selectedIndex];
            var priceInput = row.querySelector('.item-price');
            var qtyInput = row.querySelector('.item-qty');
            var lineNo = Number(row.getAttribute('data-index')) + 1;

            if (!select.value) {
                qtyInput.value = '';
                priceInput.value = '';
                recalc({
                    announceProduct: { cleared: true, lineNo: lineNo }
                });
                return;
            }

            var name = option.getAttribute('data-name') || option.textContent.trim();
            var price = option.getAttribute('data-price') || '0';

            if (!priceInput.value || toNumber(priceInput.value) === 0) {
                priceInput.value = formatMoney(price);
            }
            if (!qtyInput.value) {
                qtyInput.value = '1';
            }

            var state = getLineState(row);
            recalc({
                announceProduct: {
                    complete: state.isComplete,
                    incomplete: !state.isComplete,
                    name: name,
                    qty: state.qty
                }
            });
        });
    });

    document.querySelectorAll('.item-qty, .item-price').forEach(function (input) {
        input.addEventListener('input', function () {
            var row = input.closest('.purchase-line');
            var state = getLineState(row);
            var announce = null;
            if (state.isComplete) {
                announce = { complete: true, name: state.name, qty: state.qty };
            } else if (state.isIncomplete) {
                announce = { incomplete: true, name: state.name, qty: state.qty };
            }
            recalc({ announceProduct: announce });
        });
    });

    document.getElementById('purchaseForm').addEventListener('submit', function (e) {
        var vendor = document.getElementById('vendor_uid');
        var dateInput = document.getElementById('purchase_date');
        var reasons = [];

        if (!vendor.value) {
            reasons.push('Vendor is required.');
            vendor.classList.add('is-invalid');
        }
        if (!dateInput.value) {
            reasons.push('Purchase date is required.');
            dateInput.classList.add('is-invalid');
        }

        var filled = refreshSummary();
        if (filled < 1) {
            reasons.push('Add at least one product with quantity before saving.');
        }

        document.querySelectorAll('.purchase-line').forEach(function (row, index) {
            var state = getLineState(row);
            if (state.hasProduct && state.qty <= 0) {
                reasons.push('Line ' + (index + 1) + ': enter quantity greater than 0.');
                row.querySelector('.item-qty').classList.add('is-invalid');
            }
        });

        if (reasons.length) {
            e.preventDefault();
            showAlert('danger', 'Purchase was not saved: ' + reasons.join(' '));
            var box = document.getElementById('purchaseValidationErrors');
            if (!box) {
                var alertHost = document.getElementById('purchaseAlert');
                alertHost.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        var saveBtn = document.getElementById('savePurchaseBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving purchase...';
        showAlert('info', 'Saving purchase with ' + filled + ' item(s). Inventory will update after save.');
        recalc();
    });

    @if($errors->any() || session('error'))
    window.setTimeout(function () {
        var target = document.getElementById('purchaseValidationErrors') || document.getElementById('purchaseAlert');
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);
    @endif

    recalc();
})();
</script>
@endpush
