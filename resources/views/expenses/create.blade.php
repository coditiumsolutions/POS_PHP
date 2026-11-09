@extends('layouts.app')

@section('title', 'Add Expense')

@section('content')
@php
    $defaultBranchUid = old('branch_uid', \App\Support\CurrentBranch::uid());
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="h3 page-heading invoice-details-heading mb-0">Add Expense</h1>
        <div class="text-muted small">Expense No: <strong>{{ $expenseNo }}</strong></div>
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

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf

            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="expense_date">Expense Date *</label>
                    <div class="invoice-meta-field">
                        <input id="expense_date" type="text" name="expense_date"
                               class="form-control form-control-sm invoice-meta-input report-date-input"
                               value="{{ old('expense_date', now()->format('d-M-Y')) }}"
                               placeholder="10-Nov-2026" required autocomplete="off">
                        @error('expense_date')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="branch_uid">Branch *</label>
                    <div class="invoice-meta-field">
                        <select id="branch_uid" name="branch_uid" class="form-select form-select-sm invoice-meta-input" required>
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->uid }}" @selected((string) $defaultBranchUid === (string) $branch->uid)>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_uid')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="expense_category_uid">Category *</label>
                    <div class="invoice-meta-field">
                        <select id="expense_category_uid" name="expense_category_uid" class="form-select form-select-sm invoice-meta-input" required>
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->uid }}" @selected(old('expense_category_uid') == $category->uid)>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('expense_category_uid')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="amount">Amount *</label>
                    <div class="invoice-meta-field">
                        <input id="amount" type="number" step="0.01" min="0.01" name="amount"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('amount') }}" required>
                        @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="payment_mode">Payment Mode</label>
                    <div class="invoice-meta-field">
                        <select id="payment_mode" name="payment_mode" class="form-select form-select-sm invoice-meta-input">
                            <option value="">Select mode</option>
                            @foreach(['Cash', 'Bank Transfer', 'Cheque', 'Card'] as $mode)
                                <option value="{{ $mode }}" @selected(old('payment_mode') === $mode)>{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="invoice-meta-title" for="description">Description</label>
                    <div class="invoice-meta-field">
                        <input id="description" type="text" name="description"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('description') }}">
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Expense</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
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
});
</script>
@endpush
