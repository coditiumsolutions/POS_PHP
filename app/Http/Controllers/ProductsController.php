<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $branchUid = $request->get('branch_uid');

        $query = Product::with(['category', 'unit', 'branch']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryUid) {
            $query->where('category_uid', $categoryUid);
        }

        if ($branchUid) {
            $query->where('branch_uid', $branchUid);
        }

        $products = $query->orderBy('product_name')->paginate(50)->withQueryString();
        $categories = ProductCategory::where('is_active', true)->orderBy('category_name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();

        return view('products.index', compact('products', 'categories', 'branches'));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('category_name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $units = ProductUnit::where('is_active', true)->orderBy('unit_name')->get();

        return view('products.create', compact('categories', 'branches', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'product_code')->where(fn ($q) => $q->where('branch_uid', $request->branch_uid)),
            ],
            'product_name' => 'required|string|max:200',
            'barcode' => 'nullable|string|max:100',
            'category_uid' => 'required|exists:product_categories,uid',
            'branch_uid' => 'required|exists:branches,uid',
            'unit_uid' => 'nullable|exists:product_units,uid',
            'brand_name' => 'nullable|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ], [
            'branch_uid.required' => 'Branch is required. Each product belongs to one branch.',
            'product_code.unique' => 'This product code already exists for the selected branch.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_taxable'] = $request->boolean('is_taxable');
        $validated['created_date'] = now();
        $product = Product::create($validated);

        // One inventory record for this product + branch (starting at 0).
        Inventory::firstOrCreate(
            [
                'product_uid' => $product->uid,
                'branch_uid' => $product->branch_uid,
            ],
            [
                'warehouse_uid' => null,
                'quantity_in_stock' => 0,
                'quantity_available' => 0,
                'quantity_reserved' => 0,
                'average_cost' => $product->purchase_price ?? 0,
                'quantity_in' => 0,
                'quantity_out' => 0,
                'updated_date' => now(),
            ]
        );

        return redirect()->route('products.index')->with('success', 'Product created for the selected branch. Inventory row added.');
    }

    public function edit($uid)
    {
        $product = Product::findOrFail($uid);
        $categories = ProductCategory::where('is_active', true)->orderBy('category_name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $units = ProductUnit::where('is_active', true)->orderBy('unit_name')->get();

        return view('products.edit', compact('product', 'categories', 'branches', 'units'));
    }

    public function update(Request $request, $uid)
    {
        $product = Product::findOrFail($uid);

        $validated = $request->validate([
            'product_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'product_code')
                    ->ignore($uid, 'uid')
                    ->where(fn ($q) => $q->where('branch_uid', $request->branch_uid)),
            ],
            'product_name' => 'required|string|max:200',
            'barcode' => 'nullable|string|max:100',
            'category_uid' => 'required|exists:product_categories,uid',
            'branch_uid' => 'required|exists:branches,uid',
            'unit_uid' => 'nullable|exists:product_units,uid',
            'brand_name' => 'nullable|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ], [
            'branch_uid.required' => 'Branch is required. Each product belongs to one branch.',
            'product_code.unique' => 'This product code already exists for the selected branch.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_taxable'] = $request->boolean('is_taxable');
        $validated['updated_date'] = now();
        $product->update($validated);

        Inventory::firstOrCreate(
            [
                'product_uid' => $product->uid,
                'branch_uid' => $product->branch_uid,
            ],
            [
                'warehouse_uid' => null,
                'quantity_in_stock' => 0,
                'quantity_available' => 0,
                'quantity_reserved' => 0,
                'average_cost' => $product->purchase_price ?? 0,
                'quantity_in' => 0,
                'quantity_out' => 0,
                'updated_date' => now(),
            ]
        );

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($uid)
    {
        $product = Product::findOrFail($uid);
        $label = trim(($product->product_code ? $product->product_code . ' - ' : '') . ($product->product_name ?? 'Product'));

        $reasons = [];
        $salesCount = DB::table('sales_invoice_details')->where('product_uid', $uid)->count();
        if ($salesCount > 0) {
            $reasons[] = "{$salesCount} sales invoice line(s)";
        }
        $purchaseCount = DB::table('purchase_invoice_details')->where('product_uid', $uid)->count();
        if ($purchaseCount > 0) {
            $reasons[] = "{$purchaseCount} purchase invoice line(s)";
        }
        $adjustmentCount = DB::table('stock_adjustment_details')->where('product_uid', $uid)->count();
        if ($adjustmentCount > 0) {
            $reasons[] = "{$adjustmentCount} stock adjustment line(s)";
        }
        $supplyCount = DB::table('customer_monthly_supplies')->where('product_uid', $uid)->count();
        if ($supplyCount > 0) {
            $reasons[] = "{$supplyCount} monthly supply record(s)";
        }

        if ($reasons) {
            return back()->with(
                'error',
                "Cannot delete \"{$label}\" because it is used in " . implode(', ', $reasons) . '. Remove those records first, or mark the product inactive instead.'
            );
        }

        try {
            DB::transaction(function () use ($product, $uid) {
                DB::table('inventory_transactions')->where('product_uid', $uid)->delete();
                Inventory::where('product_uid', $uid)->delete();
                DB::table('customer_monthly_supply_overrides')->where('product_uid', $uid)->delete();
                $product->delete();
            });
        } catch (QueryException $e) {
            return back()->with(
                'error',
                "Could not delete \"{$label}\" because it is still referenced by other records in the database."
            );
        }

        return back()->with('success', "Product \"{$label}\" deleted successfully!");
    }
}
