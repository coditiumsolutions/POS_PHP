<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Branch;
use App\Support\CurrentBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Expense::with(['category', 'branch', 'user']);

        if ($request->filled('category_uid')) {
            $query->where('expense_category_uid', $request->category_uid);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(50);
        $categories = ExpenseCategory::where('is_active', true)->orderBy('category_name')->get();
        $totalAmount = $query->sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'totalAmount'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('category_name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $expenseNo = $this->generateExpenseNo();

        return view('expenses.create', compact('categories', 'branches', 'expenseNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_uid' => 'required|exists:expense_categories,uid',
            'expense_date' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'branch_uid' => 'required|exists:branches,uid',
        ], [
            'branch_uid.required' => 'Branch is required.',
        ]);

        $expenseDate = $this->parseDate($validated['expense_date']);
        if (!$expenseDate) {
            return back()->withInput()->withErrors(['expense_date' => 'Expense date is invalid. Use format like 10-Nov-2026.']);
        }

        $validated['expense_date'] = $expenseDate;
        $validated['expense_no'] = $this->generateExpenseNo();
        $validated['user_uid'] = Auth::id();
        $validated['branch_uid'] = $validated['branch_uid'] ?: CurrentBranch::uid();
        $validated['created_date'] = now();

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully!');
    }

    public function edit($uid)
    {
        $expense = Expense::findOrFail($uid);
        $categories = ExpenseCategory::where('is_active', true)->orderBy('category_name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'branches'));
    }

    public function update(Request $request, $uid)
    {
        $expense = Expense::findOrFail($uid);

        $validated = $request->validate([
            'expense_category_uid' => 'required|exists:expense_categories,uid',
            'expense_date' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'branch_uid' => 'required|exists:branches,uid',
        ], [
            'branch_uid.required' => 'Branch is required.',
        ]);

        $expenseDate = $this->parseDate($validated['expense_date']);
        if (!$expenseDate) {
            return back()->withInput()->withErrors(['expense_date' => 'Expense date is invalid. Use format like 10-Nov-2026.']);
        }

        $validated['expense_date'] = $expenseDate;
        $validated['updated_date'] = now();
        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function destroy($uid)
    {
        $expense = Expense::findOrFail($uid);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
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

    private function generateExpenseNo()
    {
        $date = now()->format('Ymd');
        $lastExpense = Expense::where('expense_no', 'like', "EXP{$date}%")
            ->orderBy('expense_no', 'desc')
            ->first();

        if ($lastExpense) {
            $lastNumber = (int) substr($lastExpense->expense_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'EXP' . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
