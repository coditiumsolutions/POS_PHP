<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDeduction;
use App\Models\EmployeePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $employees = Employee::orderBy('full_name')->paginate(50);
        return view('employees.index', compact('employees'));
    }

    public function salaryPayments(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $query = EmployeePayment::query()
            ->leftJoin('employees', 'employee_payments.employee_uid', '=', 'employees.uid')
            ->select(
                'employee_payments.*',
                'employees.employee_code',
                'employees.full_name as employee_name'
            )
            ->orderByDesc('employee_payments.payment_date')
            ->orderByDesc('employee_payments.uid');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('employees.full_name', 'like', "%{$search}%")
                    ->orWhere('employees.employee_code', 'like', "%{$search}%")
                    ->orWhere('employee_payments.payment_no', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(50)->withQueryString();

        return view('employees.salary-payments', compact('payments', 'search'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees',
            'employee_name' => 'required|string|max:200',
            'full_name' => 'nullable|string|max:200',
            'mobile_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'designation' => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|string',
            'basic_salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['full_name'] = $validated['full_name'] ?? $validated['employee_name'];
        unset($validated['employee_name']);
        if (!empty($validated['date_of_joining'])) {
            try {
                $validated['date_of_joining'] = \Carbon\Carbon::createFromFormat('d-M-Y', $validated['date_of_joining'])->format('Y-m-d');
            } catch (\Throwable $e) {
                try {
                    $validated['date_of_joining'] = \Carbon\Carbon::parse($validated['date_of_joining'])->format('Y-m-d');
                } catch (\Throwable $e2) {
                    return back()->withInput()->withErrors(['date_of_joining' => 'Joining date is invalid. Use format like 10-Nov-2026.']);
                }
            }
        } else {
            $validated['date_of_joining'] = null;
        }
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_date'] = now();
        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    public function show($uid)
    {
        $employee = Employee::findOrFail($uid);
        $allowances = EmployeeAllowance::where('employee_uid', $uid)->get();
        $deductions = EmployeeDeduction::where('employee_uid', $uid)->get();
        $payments = EmployeePayment::where('employee_uid', $uid)->orderBy('payment_date', 'desc')->paginate(20);

        return view('employees.show', compact('employee', 'allowances', 'deductions', 'payments'));
    }

    public function edit($uid)
    {
        $employee = Employee::findOrFail($uid);
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, $uid)
    {
        $employee = Employee::findOrFail($uid);

        $validated = $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $uid . ',uid',
            'employee_name' => 'required|string|max:200',
            'full_name' => 'nullable|string|max:200',
            'mobile_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'designation' => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|string',
            'basic_salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['full_name'] = $validated['full_name'] ?? $validated['employee_name'];
        unset($validated['employee_name']);
        if (!empty($validated['date_of_joining'])) {
            try {
                $validated['date_of_joining'] = \Carbon\Carbon::createFromFormat('d-M-Y', $validated['date_of_joining'])->format('Y-m-d');
            } catch (\Throwable $e) {
                try {
                    $validated['date_of_joining'] = \Carbon\Carbon::parse($validated['date_of_joining'])->format('Y-m-d');
                } catch (\Throwable $e2) {
                    return back()->withInput()->withErrors(['date_of_joining' => 'Joining date is invalid. Use format like 10-Nov-2026.']);
                }
            }
        } else {
            $validated['date_of_joining'] = null;
        }
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['updated_date'] = now();
        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function payroll(Request $request, $uid)
    {
        $employee = Employee::findOrFail($uid);
        $allowances = EmployeeAllowance::where('employee_uid', $uid)
            ->where('is_active', true)
            ->orderBy('allowance_type')
            ->get();
        $deductions = EmployeeDeduction::where('employee_uid', $uid)
            ->where('is_active', true)
            ->orderBy('deduction_type')
            ->get();
        $payments = EmployeePayment::where('employee_uid', $uid)
            ->orderByDesc('payment_date')
            ->orderByDesc('uid')
            ->paginate(20);

        $totalAllowances = (float) $allowances->sum('amount');
        $totalDeductions = (float) $deductions->sum('amount');
        $netSalary = (float) ($employee->basic_salary ?? 0) + $totalAllowances - $totalDeductions;

        $selectedMonth = $request->input('month_year', now()->format('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', (string) $selectedMonth)) {
            $selectedMonth = now()->format('Y-m');
        }

        $paidThisMonth = $this->paidForMonth($uid, $selectedMonth);
        $balanceSalary = round($netSalary - $paidThisMonth, 2);
        $remainingSalary = max(0, $balanceSalary);
        $overpaidAmount = max(0, round($paidThisMonth - $netSalary, 2));
        $activeTab = $request->input('tab', 'payments') === 'allowances' ? 'allowances' : 'payments';

        return view('employees.payroll', compact(
            'employee',
            'allowances',
            'deductions',
            'payments',
            'totalAllowances',
            'totalDeductions',
            'netSalary',
            'selectedMonth',
            'paidThisMonth',
            'remainingSalary',
            'balanceSalary',
            'overpaidAmount',
            'activeTab'
        ));
    }

    public function storeAllowance(Request $request, $uid)
    {
        Employee::findOrFail($uid);

        $validated = $request->validate([
            'allowance_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
        ]);

        EmployeeAllowance::create([
            'employee_uid' => $uid,
            'allowance_type' => $validated['allowance_type'],
            'amount' => $validated['amount'],
            'is_active' => true,
            'created_date' => now(),
        ]);

        return redirect()
            ->route('employees.payroll', ['uid' => $uid, 'tab' => 'allowances'])
            ->with('success', 'Allowance added.');
    }

    public function destroyAllowance($uid, $allowanceUid)
    {
        $allowance = EmployeeAllowance::where('employee_uid', $uid)->where('uid', $allowanceUid)->firstOrFail();
        $allowance->delete();

        return redirect()
            ->route('employees.payroll', ['uid' => $uid, 'tab' => 'allowances'])
            ->with('success', 'Allowance removed.');
    }

    public function storeDeduction(Request $request, $uid)
    {
        Employee::findOrFail($uid);

        $validated = $request->validate([
            'deduction_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
        ]);

        EmployeeDeduction::create([
            'employee_uid' => $uid,
            'deduction_type' => $validated['deduction_type'],
            'amount' => $validated['amount'],
            'is_active' => true,
            'created_date' => now(),
        ]);

        return redirect()
            ->route('employees.payroll', ['uid' => $uid, 'tab' => 'allowances'])
            ->with('success', 'Deduction added.');
    }

    public function destroyDeduction($uid, $deductionUid)
    {
        $deduction = EmployeeDeduction::where('employee_uid', $uid)->where('uid', $deductionUid)->firstOrFail();
        $deduction->delete();

        return redirect()
            ->route('employees.payroll', ['uid' => $uid, 'tab' => 'allowances'])
            ->with('success', 'Deduction removed.');
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'employee_uid' => 'required|exists:employees,uid',
            'payment_date' => 'required|string',
            'salary_month' => 'nullable|string|max:20',
            'month_year' => 'required|string|max:20',
            'basic_salary' => 'required|numeric|min:0',
            'allowances_amount' => 'nullable|numeric|min:0',
            'deductions_amount' => 'nullable|numeric|min:0',
            'total_allowances' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'net_amount' => 'nullable|numeric|min:0',
            'net_salary' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:500',
        ]);

        $paymentDate = $this->parseDate($validated['payment_date']);
        if (!$paymentDate) {
            return back()->withInput()->withErrors(['payment_date' => 'Payment date is invalid. Use format like 10-Nov-2026.']);
        }

        $monthYear = $validated['month_year'] ?? $validated['salary_month'] ?? now()->format('Y-m');
        $payAmount = round((float) $validated['net_salary'], 2);

        $employee = Employee::findOrFail($validated['employee_uid']);
        $totalAllowances = (float) EmployeeAllowance::where('employee_uid', $employee->uid)->where('is_active', true)->sum('amount');
        $totalDeductions = (float) EmployeeDeduction::where('employee_uid', $employee->uid)->where('is_active', true)->sum('amount');
        $monthNetSalary = (float) ($employee->basic_salary ?? 0) + $totalAllowances - $totalDeductions;
        $paidThisMonth = $this->paidForMonth($employee->uid, $monthYear);

        $remarks = $validated['remarks'] ?? null;
        if (!empty($validated['payment_mode'])) {
            $modeNote = 'Mode: ' . $validated['payment_mode'];
            $remarks = $remarks ? ($remarks . ' | ' . $modeNote) : $modeNote;
        }

        $payload = [
            'employee_uid' => $validated['employee_uid'],
            'payment_date' => $paymentDate,
            'month_year' => $monthYear,
            'basic_salary' => $validated['basic_salary'],
            'total_allowances' => $validated['total_allowances'] ?? $validated['allowances_amount'] ?? $totalAllowances,
            'total_deductions' => $validated['total_deductions'] ?? $validated['deductions_amount'] ?? $totalDeductions,
            'net_salary' => $payAmount,
            'amount' => $payAmount,
            'remarks' => $remarks,
            'user_uid' => Auth::id(),
            'payment_no' => $this->generatePaymentNo(),
            'created_date' => now(),
        ];

        EmployeePayment::create($payload);

        $newPaid = round($paidThisMonth + $payAmount, 2);
        $newBalance = round($monthNetSalary - $newPaid, 2);
        $message = 'Payment recorded for ' . $monthYear . '.';
        if ($newBalance < 0) {
            $message .= ' Overpaid by Rs. ' . number_format(abs($newBalance), 2) . '.';
        } else {
            $message .= ' Remaining: Rs. ' . number_format($newBalance, 2) . '.';
        }

        return redirect()
            ->route('employees.payroll', [
                'uid' => $validated['employee_uid'],
                'month_year' => $monthYear,
                'tab' => 'payments',
            ])
            ->with('success', $message);
    }

    public function destroyPayment(Request $request, $uid, $paymentUid)
    {
        $payment = EmployeePayment::where('employee_uid', $uid)
            ->where('uid', $paymentUid)
            ->firstOrFail();

        $monthYear = $payment->month_year ?: $request->input('month_year', now()->format('Y-m'));
        $payment->delete();

        return redirect()
            ->route('employees.payroll', [
                'uid' => $uid,
                'month_year' => $monthYear,
                'tab' => 'payments',
            ])
            ->with('success', 'Payment deleted successfully.');
    }

    private function paidForMonth($employeeUid, string $monthYear): float
    {
        $payments = EmployeePayment::where('employee_uid', $employeeUid)
            ->where('month_year', $monthYear)
            ->get(['amount', 'net_salary']);

        return round((float) $payments->sum(function ($payment) {
            $amount = (float) ($payment->amount ?? 0);
            if ($amount > 0) {
                return $amount;
            }

            return (float) ($payment->net_salary ?? 0);
        }), 2);
    }

    private function generatePaymentNo()
    {
        $date = now()->format('Ymd');
        $lastPayment = EmployeePayment::where('payment_no', 'like', "EP{$date}%")
            ->orderBy('payment_no', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'EP' . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function parseDate(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        foreach (['d-M-Y', 'Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
