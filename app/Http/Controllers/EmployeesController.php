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
        $employees = Employee::orderBy('employee_name')->paginate(50);
        return view('employees.index', compact('employees'));
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
            'mobile_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'designation' => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

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
            'mobile_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'designation' => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

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
            'salary_month' => 'required|string|max:20',
            'basic_salary' => 'required|numeric|min:0',
            'allowances_amount' => 'nullable|numeric|min:0',
            'deductions_amount' => 'nullable|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|string|max:50',
            'remarks' => 'nullable|string|max:500',
        ]);

        $validated['payment_no'] = $this->generatePaymentNo();
        $validated['created_date'] = now();

        EmployeePayment::create($validated);

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
