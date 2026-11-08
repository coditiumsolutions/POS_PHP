@extends('layouts.app')

@section('title', 'Salary Payments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="h3 page-heading mb-0">Salary Payments</h1>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm">All Employees</a>
    </div>

    <form method="GET" class="row g-2 align-items-center mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search employee or payment no..."
                   value="{{ $search }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Search</button>
            @if($search !== '')
                <a href="{{ route('employees.salary-payments') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Payment No</th>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Month</th>
                    <th class="text-end">Basic</th>
                    <th class="text-end">Allowances</th>
                    <th class="text-end">Deductions</th>
                    <th class="text-end">Net</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_no }}</td>
                        <td>{{ optional($payment->payment_date)->format('d-M-Y') }}</td>
                        <td>
                            {{ $payment->employee_name ?: '—' }}
                            @if($payment->employee_code)
                                <div class="small text-muted">{{ $payment->employee_code }}</div>
                            @endif
                        </td>
                        <td>{{ $payment->month_year ?? $payment->salary_month ?? '—' }}</td>
                        <td class="text-end">{{ number_format((float) $payment->basic_salary, 2) }}</td>
                        <td class="text-end">{{ number_format((float) ($payment->total_allowances ?? $payment->allowances_amount ?? 0), 2) }}</td>
                        <td class="text-end">{{ number_format((float) ($payment->total_deductions ?? $payment->deductions_amount ?? 0), 2) }}</td>
                        <td class="text-end">{{ number_format((float) ($payment->net_salary ?? $payment->net_amount ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No salary payments found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $payments->links() }}</div>
</div>
@endsection
