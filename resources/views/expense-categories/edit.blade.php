@extends('layouts.app')

@section('title', 'Edit Expense Category')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit Expense Category</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('expense-categories.update', $category->uid) }}">
            @csrf
            @method('PUT')
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="category_code">Code *</label>
                    <div class="invoice-meta-field">
                        <input id="category_code" type="text" name="category_code" class="form-control form-control-sm invoice-meta-input" value="{{ old('category_code', $category->category_code) }}" required>
                    </div>
                    <label class="invoice-meta-title" for="category_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="category_name" type="text" name="category_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('category_name', $category->category_name) }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="description">Description</label>
                    <div class="invoice-meta-field">
                        <input id="description" type="text" name="description" class="form-control form-control-sm invoice-meta-input" value="{{ old('description', $category->description) }}">
                    </div>
                    <label class="invoice-meta-title" for="is_active">Status</label>
                    <div class="invoice-meta-field">
                        <select id="is_active" name="is_active" class="form-select form-select-sm invoice-meta-input">
                            <option value="1" @selected(old('is_active', $category->is_active) == 1)>Active</option>
                            <option value="0" @selected(old('is_active', $category->is_active) == 0)>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="{{ route('expense-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
