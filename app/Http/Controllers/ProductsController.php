<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryUid = $request->get('category_uid');

        $query = Product::with(['category', 'unit']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryUid) {
            $query->where('category_uid', $categoryUid);
        }

        $products = $query->orderBy('product_name')->paginate(50);
        $categories = ProductCategory::where('is_active', true)->orderBy('category_name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('category_name')->get();
        $units = ProductUnit::where('is_active', true)->orderBy('unit_name')->get();
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:products',
            'product_name' => 'required|string|max:200',
            'barcode' => 'nullable|string|max:100',
            'category_uid' => 'nullable|exists:product_categories,uid',
            'unit_uid' => 'nullable|exists:product_units,uid',
            'brand_name' => 'nullable|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['created_date'] = now();
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit($uid)
    {
        $product = Product::findOrFail($uid);
        $categories = ProductCategory::where('is_active', true)->orderBy('category_name')->get();
        $units = ProductUnit::where('is_active', true)->orderBy('unit_name')->get();
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, $uid)
    {
        $product = Product::findOrFail($uid);

        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $uid . ',uid',
            'product_name' => 'required|string|max:200',
            'barcode' => 'nullable|string|max:100',
            'category_uid' => 'nullable|exists:product_categories,uid',
            'unit_uid' => 'nullable|exists:product_units,uid',
            'brand_name' => 'nullable|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['updated_date'] = now();
        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($uid)
    {
        $product = Product::findOrFail($uid);
        $product->update(['is_active' => false]);
        return redirect()->route('products.index')->with('success', 'Product deactivated successfully!');
    }
}
