<?php
namespace App\Http\Controllers;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoriesController extends Controller
{
    public function __construct() { $this->middleware('auth'); }
    
    public function index() {
        $categories = ProductCategory::orderBy('category_name')->paginate(50);
        return view('product-categories.index', compact('categories'));
    }
    
    public function create() { return view('product-categories.create'); }
    
    public function store(Request $request) {
        $validated = $request->validate([
            'category_code' => 'required|string|max:50|unique:product_categories',
            'category_name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_date'] = now();
        ProductCategory::create($validated);
        return redirect()->route('product-categories.index')->with('success', 'Category created!');
    }
    
    public function edit($uid) {
        $category = ProductCategory::findOrFail($uid);
        return view('product-categories.edit', compact('category'));
    }
    
    public function update(Request $request, $uid) {
        $category = ProductCategory::findOrFail($uid);
        $validated = $request->validate([
            'category_code' => 'required|string|max:50|unique:product_categories,category_code,' . $uid . ',uid',
            'category_name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_date'] = now();
        $category->update($validated);
        return redirect()->route('product-categories.index')->with('success', 'Category updated!');
    }
}
