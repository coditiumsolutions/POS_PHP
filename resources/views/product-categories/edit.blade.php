@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Edit Category</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('product-categories.update', $category->uid) }}">
            @csrf
            @method('PUT')

            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="category_code">Code *</label>
                    <div class="invoice-meta-field">
                        <input id="category_code" type="text" name="category_code"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('category_code', $category->category_code) }}" required>
                        @error('category_code')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="category_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="category_name" type="text" name="category_name"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('category_name', $category->category_name) }}" required>
                        @error('category_name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="description">Description</label>
                    <div class="invoice-meta-field">
                        <input id="description" type="text" name="description"
                               class="form-control form-control-sm invoice-meta-input"
                               value="{{ old('description', $category->description) }}">
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <label class="invoice-meta-title" for="is_active">Active</label>
                    <div class="invoice-meta-field">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input"
                               @checked(old('is_active', $category->is_active) == true || old('is_active') === '1')>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('product-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
