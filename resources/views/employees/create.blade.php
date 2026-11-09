@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
<div class="container-fluid">
    <h1 class="h3 page-heading invoice-details-heading mb-3">Add Employee</h1>

    <div class="bg-white border rounded p-3">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf
            <div class="invoice-meta-grid invoice-meta-form">
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="employee_code">Code *</label>
                    <div class="invoice-meta-field">
                        <input id="employee_code" type="text" name="employee_code" class="form-control form-control-sm invoice-meta-input" value="{{ old('employee_code') }}" required>
                    </div>
                    <label class="invoice-meta-title" for="employee_name">Name *</label>
                    <div class="invoice-meta-field">
                        <input id="employee_name" type="text" name="employee_name" class="form-control form-control-sm invoice-meta-input" value="{{ old('employee_name') }}" required>
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="designation">Designation</label>
                    <div class="invoice-meta-field">
                        <input id="designation" type="text" name="designation" class="form-control form-control-sm invoice-meta-input" value="{{ old('designation') }}">
                    </div>
                    <label class="invoice-meta-title" for="basic_salary">Basic Salary</label>
                    <div class="invoice-meta-field">
                        <input id="basic_salary" type="number" step="0.01" min="0" name="basic_salary" class="form-control form-control-sm invoice-meta-input" value="{{ old('basic_salary', '0') }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="mobile_no">Mobile</label>
                    <div class="invoice-meta-field">
                        <input id="mobile_no" type="text" name="mobile_no" class="form-control form-control-sm invoice-meta-input" value="{{ old('mobile_no') }}">
                    </div>
                    <label class="invoice-meta-title" for="email">Email</label>
                    <div class="invoice-meta-field">
                        <input id="email" type="email" name="email" class="form-control form-control-sm invoice-meta-input" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="invoice-meta-row">
                    <label class="invoice-meta-title" for="date_of_joining">Joining Date</label>
                    <div class="invoice-meta-field">
                        <input id="date_of_joining" type="text" name="date_of_joining" class="form-control form-control-sm invoice-meta-input report-date-input" value="{{ old('date_of_joining') }}" placeholder="10-Nov-2026" autocomplete="off">
                    </div>
                    <label class="invoice-meta-title" for="address">Address</label>
                    <div class="invoice-meta-field">
                        <input id="address" type="text" name="address" class="form-control form-control-sm invoice-meta-input" value="{{ old('address') }}">
                    </div>
                </div>
            </div>
            <input type="hidden" name="is_active" value="1">
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
<script>document.addEventListener('DOMContentLoaded', function () { flatpickr('.report-date-input', { dateFormat: 'd-M-Y', allowInput: true, disableMobile: true }); });</script>
@endpush
