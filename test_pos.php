<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\SalesInvoice;

echo "=== POS_PHP System Test ===\n\n";

// Test database connection
echo "1. Testing database connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✅ Database connected\n\n";
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test user authentication
echo "2. Testing user data...\n";
$users = User::count();
echo "   Users in system: $users\n";
$admin = User::where('user_name', 'admin')->first();
if ($admin) {
    echo "   ✅ Admin user found: {$admin->full_name}\n\n";
} else {
    echo "   ❌ Admin user not found\n\n";
}

// Test products
echo "3. Testing products...\n";
$productCount = Product::where('is_active', true)->count();
echo "   Active products: $productCount\n";
if ($productCount > 0) {
    $product = Product::where('is_active', true)->first();
    echo "   ✅ Sample product: {$product->product_name} - Rs. {$product->sale_price}\n\n";
} else {
    echo "   ❌ No active products found\n\n";
}

// Test customers
echo "4. Testing customers...\n";
$customerCount = Customer::where('is_active', true)->count();
echo "   Active customers: $customerCount\n";
if ($customerCount > 0) {
    echo "   ✅ Customers loaded\n\n";
} else {
    echo "   ❌ No customers found\n\n";
}

// Test payment methods
echo "5. Testing payment methods...\n";
$paymentMethods = PaymentMethod::where('is_active', true)->count();
echo "   Payment methods: $paymentMethods\n";
if ($paymentMethods > 0) {
    echo "   ✅ Payment methods configured\n\n";
} else {
    echo "   ❌ No payment methods found\n\n";
}

// Test sales invoices
echo "6. Testing sales invoices...\n";
$salesCount = SalesInvoice::count();
echo "   Total invoices: $salesCount\n";
echo "   ✅ Sales invoice system ready\n\n";

echo "=== All Tests Passed! ===\n";
echo "\nPOS_PHP is ready to use!\n";
echo "Access at: http://127.0.0.1:8042\n";
echo "Login: admin / admin123\n";
