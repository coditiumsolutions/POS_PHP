@extends('layouts.app')

@section('title', 'Employee Payroll')

@section('content')
@php
    $defaultPayAmount = old('net_salary', number_format((float) ($remainingSalary > 0 ? $remainingSalary : 0), 2, '.', ''));
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading invoice-details-heading mb-1">Payroll</h1>
            <p class="text-muted mb-0">
                {{ $employee->full_name }}
                <span class="small">({{ $employee->employee_code }})</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('employees.edit', $employee->uid) }}" class="btn btn-outline-secondary btn-sm">Edit Employee</a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm">All Employees</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border rounded p-3 mb-3">
        <div class="invoice-meta-grid invoice-meta-form">
            <div class="invoice-meta-row">
                <label class="invoice-meta-title">Basic Salary</label>
                <div class="invoice-meta-field">
                    <input type="text" class="form-control form-control-sm invoice-meta-input" value="Rs. {{ number_format((float) $employee->basic_salary, 2) }}" readonly>
                </div>
                <label class="invoice-meta-title">Allowances</label>
                <div class="invoice-meta-field">
                    <input type="text" class="form-control form-control-sm invoice-meta-input" value="Rs. {{ number_format((float) $totalAllowances, 2) }}" readonly>
                </div>
            </div>
            <div class="invoice-meta-row">
                <label class="invoice-meta-title">Deductions</label>
                <div class="invoice-meta-field">
                    <input type="text" class="form-control form-control-sm invoice-meta-input" value="Rs. {{ number_format((float) $totalDeductions, 2) }}" readonly>
                </div>
                <label class="invoice-meta-title">Net Salary</label>
                <div class="invoice-meta-field">
                    <input type="text" class="form-control form-control-sm invoice-meta-input fw-bold" value="Rs. {{ number_format((float) $netSalary, 2) }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="payrollTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'payments' ? 'active' : '' }}" id="payments-tab" data-bs-toggle="tab"
                    data-bs-target="#paymentsPane" type="button" role="tab" aria-controls="paymentsPane"
                    aria-selected="{{ $activeTab === 'payments' ? 'true' : 'false' }}">
                Record Payment
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'allowances' ? 'active' : '' }}" id="allowances-tab" data-bs-toggle="tab"
                    data-bs-target="#allowancesPane" type="button" role="tab" aria-controls="allowancesPane"
                    aria-selected="{{ $activeTab === 'allowances' ? 'true' : 'false' }}">
                Allowances &amp; Deductions
            </button>
        </li>
    </ul>

    <div class="tab-content" id="payrollTabsContent">
        <div class="tab-pane fade {{ $activeTab === 'payments' ? 'show active' : '' }}" id="paymentsPane" role="tabpanel" aria-labelledby="payments-tab">
            <div class="bg-white border rounded p-3 mb-3">
                <strong class="d-block mb-2">Record Salary Payment</strong>

                @if($overpaidAmount > 0)
                    <div class="alert alert-warning py-2 mb-3">
                        Overpaid for {{ $selectedMonth }}: Paid Rs. {{ number_format((float) $paidThisMonth, 2) }}
                        against Net Salary Rs. {{ number_format((float) $netSalary, 2) }}
                        (over by Rs. {{ number_format((float) $overpaidAmount, 2) }}).
                        You can still record more payments.
                    </div>
                @elseif($remainingSalary <= 0)
                    <div class="alert alert-info py-2 mb-3">
                        This month is fully paid. You can still record additional payments if needed.
                    </div>
                @endif

                <div class="invoice-meta-grid invoice-meta-form mb-3">
                    <div class="invoice-meta-row">
                        <label class="invoice-meta-title">Net Salary</label>
                        <div class="invoice-meta-field">
                            <input type="text" class="form-control form-control-sm invoice-meta-input" value="Rs. {{ number_format((float) $netSalary, 2) }}" readonly>
                        </div>
                        <label class="invoice-meta-title">Paid This Month</label>
                        <div class="invoice-meta-field">
                            <input type="text" class="form-control form-control-sm invoice-meta-input" value="Rs. {{ number_format((float) $paidThisMonth, 2) }}" readonly>
                        </div>
                    </div>
                    <div class="invoice-meta-row">
                        <label class="invoice-meta-title">Remaining</label>
                        <div class="invoice-meta-field">
                            <input type="text" class="form-control form-control-sm invoice-meta-input fw-bold {{ $balanceSalary < 0 ? 'text-warning' : ($balanceSalary == 0 ? 'text-success' : 'text-danger') }}"
                                   value="Rs. {{ number_format((float) $balanceSalary, 2) }}" readonly>
                        </div>
                        <label class="invoice-meta-title"></label>
                        <div class="invoice-meta-field text-muted small">
                            Remaining = Net Salary − payments for selected month
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('employees.store-payment') }}" id="salaryPaymentForm">
                    @csrf
                    <input type="hidden" name="employee_uid" value="{{ $employee->uid }}">
                    <input type="hidden" name="basic_salary" value="{{ (float) $employee->basic_salary }}">
                    <input type="hidden" name="total_allowances" value="{{ (float) $totalAllowances }}">
                    <input type="hidden" name="total_deductions" value="{{ (float) $totalDeductions }}">

                    <div class="invoice-meta-grid invoice-meta-form">
                        <div class="invoice-meta-row">
                            <label class="invoice-meta-title" for="payment_date">Payment Date *</label>
                            <div class="invoice-meta-field">
                                <input id="payment_date" type="text" name="payment_date"
                                       class="form-control form-control-sm invoice-meta-input report-date-input"
                                       value="{{ old('payment_date', now()->format('d-M-Y')) }}"
                                       placeholder="10-Nov-2026" required autocomplete="off">
                            </div>
                            <label class="invoice-meta-title" for="month_year">Salary Month *</label>
                            <div class="invoice-meta-field">
                                <input id="month_year" type="month" name="month_year"
                                       class="form-control form-control-sm invoice-meta-input"
                                       value="{{ old('month_year', $selectedMonth) }}" required>
                            </div>
                        </div>
                        <div class="invoice-meta-row">
                            <label class="invoice-meta-title" for="payment_mode">Payment Mode</label>
                            <div class="invoice-meta-field">
                                <select id="payment_mode" name="payment_mode" class="form-select form-select-sm invoice-meta-input">
                                    @foreach(['Cash', 'Bank Transfer', 'Cheque'] as $mode)
                                        <option value="{{ $mode }}" @selected(old('payment_mode') === $mode)>{{ $mode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="invoice-meta-title" for="net_salary">Pay Amount *</label>
                            <div class="invoice-meta-field">
                                <input id="net_salary" type="number" step="0.01" min="0.01" name="net_salary"
                                       class="form-control form-control-sm invoice-meta-input fw-bold"
                                       value="{{ $defaultPayAmount }}" required>
                                @error('net_salary')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="invoice-meta-row">
                            <label class="invoice-meta-title" for="remarks">Remarks</label>
                            <div class="invoice-meta-field" style="grid-column: 2 / -1;">
                                <input id="remarks" type="text" name="remarks"
                                       class="form-control form-control-sm invoice-meta-input"
                                       value="{{ old('remarks') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">Record Payment</button>
                    </div>
                </form>
            </div>

            <div class="bg-white border rounded p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Payment History</strong>
                    <a href="{{ route('employees.salary-payments') }}" class="btn btn-sm btn-outline-secondary">All Salary Payments</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Payment No</th>
                                <th>Date</th>
                                <th>Month</th>
                                <th class="text-end">Basic</th>
                                <th class="text-end">Allowances</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Paid</th>
                                <th>Remarks</th>
                                <th style="width: 70px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_no }}</td>
                                    <td>{{ optional($payment->payment_date)->format('d-M-Y') }}</td>
                                    <td>{{ $payment->month_year ?: '—' }}</td>
                                    <td class="text-end">{{ number_format((float) $payment->basic_salary, 2) }}</td>
                                    <td class="text-end">{{ number_format((float) $payment->total_allowances, 2) }}</td>
                                    <td class="text-end">{{ number_format((float) $payment->total_deductions, 2) }}</td>
                                    <td class="text-end">{{ number_format((float) ($payment->amount ?: $payment->net_salary), 2) }}</td>
                                    <td>{{ $payment->remarks ?: '—' }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('employees.destroy-payment', [$employee->uid, $payment->uid]) }}"
                                              onsubmit="return confirm('Delete payment {{ $payment->payment_no }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="month_year" value="{{ $selectedMonth }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-3">No payments recorded yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">{{ $payments->links() }}</div>
            </div>
        </div>

        <div class="tab-pane fade {{ $activeTab === 'allowances' ? 'show active' : '' }}" id="allowancesPane" role="tabpanel" aria-labelledby="allowances-tab">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="bg-white border rounded p-3 h-100">
                        <strong class="d-block mb-2">Allowances</strong>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th class="text-end">Amount</th>
                                        <th style="width: 70px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allowances as $allowance)
                                        <tr>
                                            <td>{{ $allowance->allowance_type }}</td>
                                            <td class="text-end">{{ number_format((float) $allowance->amount, 2) }}</td>
                                            <td class="text-end">
                                                <form method="POST" action="{{ route('employees.destroy-allowance', [$employee->uid, $allowance->uid]) }}"
                                                      onsubmit="return confirm('Remove this allowance?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Del</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted text-center">No allowances</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <form method="POST" action="{{ route('employees.store-allowance', $employee->uid) }}">
                            @csrf
                            <div class="invoice-meta-grid invoice-meta-form">
                                <div class="invoice-meta-row">
                                    <label class="invoice-meta-title" for="allowance_type">Type *</label>
                                    <div class="invoice-meta-field">
                                        <input id="allowance_type" type="text" name="allowance_type" class="form-control form-control-sm invoice-meta-input" required>
                                    </div>
                                    <label class="invoice-meta-title" for="allowance_amount">Amount *</label>
                                    <div class="invoice-meta-field">
                                        <input id="allowance_amount" type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm invoice-meta-input" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Add Allowance</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="bg-white border rounded p-3 h-100">
                        <strong class="d-block mb-2">Deductions</strong>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th class="text-end">Amount</th>
                                        <th style="width: 70px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deductions as $deduction)
                                        <tr>
                                            <td>{{ $deduction->deduction_type }}</td>
                                            <td class="text-end">{{ number_format((float) $deduction->amount, 2) }}</td>
                                            <td class="text-end">
                                                <form method="POST" action="{{ route('employees.destroy-deduction', [$employee->uid, $deduction->uid]) }}"
                                                      onsubmit="return confirm('Remove this deduction?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Del</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted text-center">No deductions</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <form method="POST" action="{{ route('employees.store-deduction', $employee->uid) }}">
                            @csrf
                            <div class="invoice-meta-grid invoice-meta-form">
                                <div class="invoice-meta-row">
                                    <label class="invoice-meta-title" for="deduction_type">Type *</label>
                                    <div class="invoice-meta-field">
                                        <input id="deduction_type" type="text" name="deduction_type" class="form-control form-control-sm invoice-meta-input" required>
                                    </div>
                                    <label class="invoice-meta-title" for="deduction_amount">Amount *</label>
                                    <div class="invoice-meta-field">
                                        <input id="deduction_amount" type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm invoice-meta-input" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Add Deduction</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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

    const monthInput = document.getElementById('month_year');
    if (monthInput) {
        monthInput.addEventListener('change', function () {
            const url = new URL(@json(route('employees.payroll', $employee->uid)), window.location.origin);
            url.searchParams.set('month_year', this.value);
            url.searchParams.set('tab', 'payments');
            window.location.href = url.toString();
        });
    }
});
</script>
@endpush
