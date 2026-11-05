<?php
namespace App\Http\Controllers;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoriesController extends Controller
{
    public function __construct() { $this->middleware('auth'); }
    
    public function index() {
        $categories = ExpenseCategory::orderBy('category_name')->paginate(50);
        return view('expense-categories.index', compact('categories'));
    }
    
    public function create() { return view('expense-categories.create'); }
    
    public function store(Request $request) {
        $validated = $request->validate([
            'category_code' => 'required|string|max:50|unique:expense_categories',
            'category_name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);
        ExpenseCategory::create($validated);
        return redirect()->route('expense-categories.index')->with('success', 'Category created!');
    }
    
    public function edit($uid) {
        $category = ExpenseCategory::findOrFail($uid);
        return view('expense-categories.edit', compact('category'));
    }
    
    public function update(Request $request, $uid) {
        $category = ExpenseCategory::findOrFail($uid);
        $validated = $request->validate([
            'category_code' => 'required|string|max:50|unique:expense_categories,category_code,' . $uid . ',uid',
            'category_name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);
        $category->update($validated);
        return redirect()->route('expense-categories.index')->with('success', 'Category updated!');
    }
}
