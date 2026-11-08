<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDeduction;
use App\Models\EmployeePayment;
use Illuminate\Http\Request;
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
            'date_of_joining' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['full_name'] = $validated['full_name'] ?? $validated['employee_name'];
        unset($validated['employee_name']);
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
            'date_of_joining' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['full_name'] = $validated['full_name'] ?? $validated['employee_name'];
        unset($validated['employee_name']);
        $validated['updated_date'] = now();
        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function payroll($uid)
    {
        $employee = Employee::findOrFail($uid);
        $allowances = EmployeeAllowance::where('employee_uid', $uid)->get();
        $deductions = EmployeeDeduction::where('employee_uid', $uid)->get();

        $totalAllowances = $allowances->sum('amount');
        $totalDeductions = $deductions->sum('amount');
        $netSalary = ($employee->basic_salary ?? 0) + $totalAllowances - $totalDeductions;

        return view('employees.payroll', compact('employee', 'allowances', 'deductions', 'totalAllowances', 'totalDeductions', 'netSalary'));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'employee_uid' => 'required|exists:employees,uid',
            'payment_date' => 'required|date',
            'salary_month' => 'nullable|string|max:20',
            'month_year' => 'nullable|string|max:20',
            'basic_salary' => 'required|numeric|min:0',
            'allowances_amount' => 'nullable|numeric|min:0',
            'deductions_amount' => 'nullable|numeric|min:0',
            'total_allowances' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'net_amount' => 'nullable|numeric|min:0',
            'net_salary' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:500',
        ]);

        $payload = [
            'employee_uid' => $validated['employee_uid'],
            'payment_date' => $validated['payment_date'],
            'month_year' => $validated['month_year'] ?? $validated['salary_month'] ?? now()->format('Y-m'),
            'basic_salary' => $validated['basic_salary'],
            'total_allowances' => $validated['total_allowances'] ?? $validated['allowances_amount'] ?? 0,
            'total_deductions' => $validated['total_deductions'] ?? $validated['deductions_amount'] ?? 0,
            'net_salary' => $validated['net_salary'] ?? $validated['net_amount'] ?? 0,
            'remarks' => $validated['remarks'] ?? null,
            'payment_no' => $this->generatePaymentNo(),
            'created_date' => now(),
        ];

        EmployeePayment::create($payload);

        return redirect()->route('employees.show', $validated['employee_uid'])
            ->with('success', 'Salary payment recorded successfully!');
    }

    private function generatePaymentNo()
    {
        $date = now()->format('Ymd');
        $lastPayment = EmployeePayment::where('payment_no', 'like', "EP{$date}%")
            ->orderBy('payment_no', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int)substr($lastPayment->payment_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "EP{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
