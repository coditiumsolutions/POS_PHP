@extends('layouts.app')

@section('title', 'Add Expense Category')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Expense Category</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('expense-categories.store') }}">
            @csrf
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="category_code">Code *</label>
                    <div class="invoice-meta-field">
                        <input id="category_code" type="text" name="category_code" class="form-control form-control-sm invoice-meta-input" value="{{ old('category_code') }}" required>
                    </div>
                    <label class="invoice-meta-title" for="category_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="category_name" type="text" name="category_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('category_name') }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="description">Description</label>
                    <div class="invoice-meta-field" style="grid-column: 2 / -1;">
                        <input id="description" type="text" name="description" class="form-control form-control-sm invoice-meta-input" value="{{ old('description') }}">
                    </div>
                </div>
            </div>
            <input type="hidden" name="is_active" value="1">
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Category</button>
                <a href="{{ route('expense-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
