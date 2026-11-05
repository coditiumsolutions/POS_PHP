#!/bin/bash
set -e

cd /workspace

echo "Creating remaining controller implementations..."

# Product Categories Controller
cat > app/Http/Controllers/ProductCategoriesController.php << 'PHPEOF'
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
            'is_active' => 'boolean',
        ]);
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
            'is_active' => 'boolean',
        ]);
        $validated['updated_date'] = now();
        $category->update($validated);
        return redirect()->route('product-categories.index')->with('success', 'Category updated!');
    }
}
PHPEOF

# Expense Categories Controller
cat > app/Http/Controllers/ExpenseCategoriesController.php << 'PHPEOF'
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
PHPEOF

# Warehouses Controller
cat > app/Http/Controllers/WarehousesController.php << 'PHPEOF'
<?php
namespace App\Http\Controllers;
use App\Models\Warehouse;
use Illuminate\Http/Request;

class WarehousesController extends Controller
{
    public function __construct() { $this->middleware('auth'); }
    
    public function index() {
        $warehouses = Warehouse::orderBy('warehouse_name')->paginate(50);
        return view('warehouses.index', compact('warehouses'));
    }
    
    public function create() {
        $code = $this->generateCode();
        return view('warehouses.create', compact('code'));
    }
    
    public function store(Request $request) {
        $validated = $request->validate([
            'warehouse_name' => 'required|string|max:150',
            'location' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:150',
            'mobile_no' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);
        $validated['warehouse_code'] = $this->generateCode();
        $validated['created_date'] = now();
        Warehouse::create($validated);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse created!');
    }
    
    public function edit($uid) {
        $warehouse = Warehouse::findOrFail($uid);
        return view('warehouses.edit', compact('warehouse'));
    }
    
    public function update(Request $request, $uid) {
        $warehouse = Warehouse::findOrFail($uid);
        $validated = $request->validate([
            'warehouse_code' => 'required|string|max:50|unique:warehouses,warehouse_code,' . $uid . ',uid',
            'warehouse_name' => 'required|string|max:150',
            'location' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:150',
            'mobile_no' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);
        $validated['updated_date'] = now();
        $warehouse->update($validated);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated!');
    }
    
    private function generateCode() {
        $last = Warehouse::where('warehouse_code', 'like', 'WH%')->orderBy('warehouse_code', 'desc')->first();
        $num = $last ? ((int)str_replace('WH', '', $last->warehouse_code)) + 1 : 1;
        return 'WH' . str_pad($num, 2, '0', STR_PAD_LEFT);
    }
}
PHPEOF

echo "✓ Created admin controllers"

# Now create all routes
echo "Updating routes..."

# Add to routes file
cat >> routes/web.php << 'ROUTESEOF'

    // Sales Returns
    Route::resource('sales-returns', SalesReturnsController::class);
    
    // Customer & Vendor Payments
    Route::resource('customer-payments', CustomerPaymentsController::class);
    Route::resource('vendor-payments', VendorPaymentsController::class);
    
    // Stock Adjustments
    Route::resource('stock-adjustments', StockAdjustmentsController::class);
    
    // Product Categories & Expense Categories
    Route::resource('product-categories', ProductCategoriesController::class);
    Route::resource('expense-categories', ExpenseCategoriesController::class);
    
    // Warehouses
    Route::resource('warehouses', WarehousesController::class);
ROUTESEOF

echo "✓ Routes updated"
echo "All controllers and routes created successfully!"

