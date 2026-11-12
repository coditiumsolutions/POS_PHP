@extends('layouts.app')

@section('title', 'Employee Salary')

@section('content')
@php
    $defaultPayAmount = old('net_salary', number_format((float) ($remainingSalary > 0 ? $remainingSalary : 0), 2, '.', ''));
    $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('M-Y');
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 page-heading invoice-details-heading mb-1">Employee Salary</h1>
            <p class="text-muted mb-0">
                {{ $employee->full_name }}
                <span class="small">({{ $employee->employee_code }})</span>
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <form method="GET" action="{{ route('employees.payroll', $employee->uid) }}" class="d-flex align-items-center gap-2">
                <label class="small mb-0" for="month_year_top">Month</label>
                <input id="month_year_top" type="month" name="month_year" class="form-control form-control-sm"
                       value="{{ $selectedMonth }}" onchange="this.form.submit()" style="width: 10rem;">
            </form>
            <a href="{{ route('employees.edit', $employee->uid) }}" class="btn btn-outline-secondary btn-sm">Edit Employee</a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm">All Employees</a>
        </div>
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

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg">
            <div class="border rounded p-3 h-100 bg-white">
                <div class="text-muted small">Basic Salary</div>
                <div class="h5 mb-0">Rs. {{ number_format((float) $employee->basic_salary, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="border rounded p-3 h-100 bg-white">
                <div class="text-muted small">Allowances</div>
                <div class="h5 mb-0">Rs. {{ number_format((float) $totalAllowances, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="border rounded p-3 h-100 bg-white">
                <div class="text-muted small">Deductions</div>
                <div class="h5 mb-0">Rs. {{ number_format((float) $totalDeductions, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="border rounded p-3 h-100" style="background:#e8f1ff;">
                <div class="text-muted small">Net Salary ({{ $monthLabel }})</div>
                <div class="h5 mb-0">Rs. {{ number_format((float) $netSalary, 2) }}</div>
                <div class="small text-muted">Basic + Allowances − Deductions</div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="border rounded p-3 h-100" style="background:#eaf8ef;">
                <div class="text-muted small">Paid This Month</div>
                <div class="h5 mb-0">Rs. {{ number_format((float) $paidThisMonth, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="border rounded p-3 h-100" style="background:{{ $balanceSalary > 0 ? '#fff6e8' : ($balanceSalary < 0 ? '#fde8e8' : '#eaf8ef') }};">
                <div class="text-muted small">Remaining Salary</div>
                <div class="h5 mb-0">Rs. {{ number_format((float) $balanceSalary, 2) }}</div>
                <div class="small text-muted">Net − Paid (can pay in parts)</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="bg-white border rounded p-3 h-100">
                <strong class="d-block mb-2">Allowances</strong>
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th class="text-end">Amount</th>
                                <th style="width:70px;"></th>
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
                                            <input type="hidden" name="month_year" value="{{ $selectedMonth }}">
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
                <form method="POST" action="{{ route('employees.store-allowance', $employee->uid) }}" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="month_year" value="{{ $selectedMonth }}">
                    <div class="col-md-5">
                        <label class="form-label small mb-0" for="allowance_type">Type</label>
                        <input id="allowance_type" type="text" name="allowance_type" class="form-control form-control-sm" placeholder="e.g. Transport" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-0" for="allowance_amount">Amount</label>
                        <input id="allowance_amount" type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary w-100">Add</button>
                    </div>
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
                                <th style="width:70px;"></th>
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
                                            <input type="hidden" name="month_year" value="{{ $selectedMonth }}">
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
                <form method="POST" action="{{ route('employees.store-deduction', $employee->uid) }}" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="month_year" value="{{ $selectedMonth }}">
                    <div class="col-md-5">
                        <label class="form-label small mb-0" for="deduction_type">Type</label>
                        <input id="deduction_type" type="text" name="deduction_type" class="form-control form-control-sm" placeholder="e.g. Advance" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-0" for="deduction_amount">Amount</label>
                        <input id="deduction_amount" type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded p-3 mb-3">
        <strong class="d-block mb-2">Record Payment ({{ $monthLabel }})</strong>
        <p class="text-muted small mb-3">You can pay salary in multiple payments. Remaining updates after each payment.</p>

        @if($overpaidAmount > 0)
            <div class="alert alert-warning py-2">Overpaid by Rs. {{ number_format((float) $overpaidAmount, 2) }} for this month.</div>
        @elseif($remainingSalary <= 0 && $netSalary > 0)
            <div class="alert alert-success py-2">This month is fully paid.</div>
        @endif

        <form method="POST" action="{{ route('employees.store-payment') }}">
            @csrf
            <input type="hidden" name="employee_uid" value="{{ $employee->uid }}">
            <input type="hidden" name="basic_salary" value="{{ (float) $employee->basic_salary }}">
            <input type="hidden" name="total_allowances" value="{{ (float) $totalAllowances }}">
            <input type="hidden" name="total_deductions" value="{{ (float) $totalDeductions }}">
            <input type="hidden" name="month_year" value="{{ $selectedMonth }}">

            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-0" for="payment_date">Payment Date *</label>
                    <input id="payment_date" type="text" name="payment_date"
                           class="form-control form-control-sm report-date-input"
                           value="{{ old('payment_date', now()->format('d-M-Y')) }}"
                           placeholder="10-Nov-2026" required autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0" for="payment_mode">Mode</label>
                    <select id="payment_mode" name="payment_mode" class="form-select form-select-sm">
                        @foreach(['Cash', 'Bank Transfer', 'Cheque'] as $mode)
                            <option value="{{ $mode }}" @selected(old('payment_mode') === $mode)>{{ $mode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-0" for="net_salary">Pay Amount *</label>
                    <input id="net_salary" type="number" step="0.01" min="0.01" name="net_salary"
                           class="form-control form-control-sm fw-bold"
                           value="{{ $defaultPayAmount }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0" for="remarks">Remarks</label>
                    <input id="remarks" type="text" name="remarks" class="form-control form-control-sm" value="{{ old('remarks') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Save Payment</button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white border rounded p-3">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <strong>Payments for {{ $monthLabel }}</strong>
            <a href="{{ route('employees.salary-payments') }}" class="btn btn-sm btn-outline-secondary">All Salary Payments</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Payment No</th>
                        <th>Date</th>
                        <th class="text-end">Paid</th>
                        <th>Remarks</th>
                        <th style="width:70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_no }}</td>
                            <td>{{ optional($payment->payment_date)->format('d-M-Y') }}</td>
                            <td class="text-end">{{ number_format((float) ($payment->amount ?: $payment->net_salary), 2) }}</td>
                            <td>{{ $payment->remarks ?: '—' }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('employees.destroy-payment', [$employee->uid, $payment->uid]) }}"
                                      onsubmit="return confirm('Delete payment {{ $payment->payment_no }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="month_year" value="{{ $selectedMonth }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Del</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No payments for this month</td></tr>
                    @endforelse
                </tbody>
                @if($payments->count())
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="2">Total Paid</th>
                            <th class="text-end">{{ number_format((float) $paidThisMonth, 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th colspan="2">Remaining Salary</th>
                            <th class="text-end">{{ number_format((float) $balanceSalary, 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
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
});
</script>
@endpush
