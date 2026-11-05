<?php
// Fix all models to have proper fillable arrays

$models = [
    'ProductCategory' => ['category_code', 'category_name', 'description', 'is_active'],
    'ProductUnit' => ['unit_code', 'unit_name', 'is_active'],
    'Product' => ['product_code', 'product_name', 'barcode', 'unit_price', 'sale_price', 'category_uid', 'unit_uid', 'is_active', 'created_date', 'updated_date'],
    'Customer' => ['customer_code', 'customer_name', 'mobile_no', 'phone_no', 'email', 'address', 'city', 'credit_limit', 'is_active', 'created_date', 'updated_date'],
    'Vendor' => ['vendor_code', 'vendor_name', 'mobile_no', 'phone_no', 'email', 'address', 'city', 'is_active', 'created_date', 'updated_date'],
    'Branch' => ['branch_code', 'branch_name', 'address', 'city', 'contact_person', 'mobile_no', 'phone_no', 'email', 'is_active', 'created_date', 'updated_date'],
    'Warehouse' => ['warehouse_code', 'warehouse_name', 'location', 'contact_person', 'mobile_no', 'is_active', 'created_date', 'updated_date'],
    'Employee' => ['employee_code', 'employee_name', 'designation', 'mobile_no', 'email', 'address', 'date_of_joining', 'basic_salary', 'is_active', 'created_date', 'updated_date'],
    'ExpenseCategory' => ['category_code', 'category_name', 'description', 'is_active'],
    'Expense' => ['expense_no', 'expense_date', 'category_uid', 'amount', 'payment_mode', 'description', 'user_uid', 'branch', 'created_date'],
];

foreach ($models as $modelName => $fillable) {
    $file = "/workspace/app/Models/{$modelName}.php";
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    
    // Check if fillable already exists
    if (strpos($content, 'protected $fillable') !== false) {
        echo "✓ {$modelName} already has fillable\n";
        continue;
    }
    
    // Add fillable array after "use HasFactory;"
    $fillableStr = "protected \$fillable = [\n        '" . implode("',\n        '", $fillable) . "',\n    ];";
    
    $content = preg_replace(
        '/(use HasFactory;)/',
        "$1\n\n    {$fillableStr}",
        $content
    );
    
    file_put_contents($file, $content);
    echo "✓ Fixed {$modelName}\n";
}

echo "\nAll models fixed!\n";
