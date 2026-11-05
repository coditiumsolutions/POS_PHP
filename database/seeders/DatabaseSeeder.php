<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default branch
        DB::table('branches')->insert([
            'branch_code' => 'MAIN',
            'branch_name' => 'Main Branch',
            'address' => 'Main Street',
            'city' => 'City',
            'contact_person' => 'Admin',
            'mobile_no' => '0300-1234567',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create admin user
        DB::table('users')->insert([
            'user_name' => 'admin',
            'full_name' => 'System Administrator',
            'password_hash' => Hash::make('admin123'),
            'email' => 'admin@pos.com',
            'role_name' => 'Administrator',
            'branch' => 'Main Branch',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create cashier user
        DB::table('users')->insert([
            'user_name' => 'cashier',
            'full_name' => 'Cashier User',
            'password_hash' => Hash::make('cashier123'),
            'email' => 'cashier@pos.com',
            'role_name' => 'Cashier',
            'branch' => 'Main Branch',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create manager user
        DB::table('users')->insert([
            'user_name' => 'manager',
            'full_name' => 'Manager User',
            'password_hash' => Hash::make('manager123'),
            'email' => 'manager@pos.com',
            'role_name' => 'Manager',
            'branch' => 'Main Branch',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create payment methods
        $paymentMethods = [
            ['payment_method_name' => 'Cash', 'is_active' => true],
            ['payment_method_name' => 'Credit Card', 'is_active' => true],
            ['payment_method_name' => 'Debit Card', 'is_active' => true],
            ['payment_method_name' => 'Bank Transfer', 'is_active' => true],
            ['payment_method_name' => 'Cheque', 'is_active' => true],
            ['payment_method_name' => 'Mobile Payment', 'is_active' => true],
        ];
        foreach ($paymentMethods as $method) {
            DB::table('payment_methods')->insert($method);
        }

        // Create default warehouse
        DB::table('warehouses')->insert([
            'warehouse_code' => 'WH01',
            'warehouse_name' => 'Main Warehouse',
            'location' => 'Main Branch',
            'contact_person' => 'Admin',
            'mobile_no' => '0300-1234567',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create default terminal
        DB::table('terminals')->insert([
            'terminal_code' => 'T01',
            'terminal_name' => 'Terminal 1',
            'location' => 'Counter 1',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create product units
        $units = [
            ['unit_code' => 'PC', 'unit_name' => 'Piece', 'is_active' => true],
            ['unit_code' => 'KG', 'unit_name' => 'Kilogram', 'is_active' => true],
            ['unit_code' => 'LTR', 'unit_name' => 'Liter', 'is_active' => true],
            ['unit_code' => 'MTR', 'unit_name' => 'Meter', 'is_active' => true],
            ['unit_code' => 'BOX', 'unit_name' => 'Box', 'is_active' => true],
            ['unit_code' => 'CTN', 'unit_name' => 'Carton', 'is_active' => true],
            ['unit_code' => 'DOZ', 'unit_name' => 'Dozen', 'is_active' => true],
        ];
        foreach ($units as $unit) {
            DB::table('product_units')->insert($unit);
        }

        // Create product categories
        $categories = [
            [
                'category_code' => 'GEN',
                'category_name' => 'General',
                'description' => 'General Products',
                'is_active' => true,
                'created_date' => now(),
            ],
            [
                'category_code' => 'DAIRY',
                'category_name' => 'Dairy Products',
                'description' => 'Milk, Yogurt, Cheese etc.',
                'is_active' => true,
                'created_date' => now(),
            ],
            [
                'category_code' => 'BAKE',
                'category_name' => 'Bakery',
                'description' => 'Bread, Biscuits etc.',
                'is_active' => true,
                'created_date' => now(),
            ],
        ];
        foreach ($categories as $category) {
            DB::table('product_categories')->insert($category);
        }

        // Create expense categories
        $expenseCategories = [
            ['category_code' => 'UTIL', 'category_name' => 'Utilities', 'description' => 'Electricity, Water, Gas', 'is_active' => true],
            ['category_code' => 'SAL', 'category_name' => 'Salaries', 'description' => 'Staff Salaries', 'is_active' => true],
            ['category_code' => 'RENT', 'category_name' => 'Rent', 'description' => 'Building Rent', 'is_active' => true],
            ['category_code' => 'MAINT', 'category_name' => 'Maintenance', 'description' => 'Repairs and Maintenance', 'is_active' => true],
            ['category_code' => 'OTHER', 'category_name' => 'Other', 'description' => 'Miscellaneous Expenses', 'is_active' => true],
        ];
        foreach ($expenseCategories as $category) {
            DB::table('expense_categories')->insert($category);
        }

        // Create sample products
        $products = [
            [
                'product_code' => 'P001',
                'product_name' => 'Fresh Milk 1L',
                'category_uid' => 2,
                'unit_uid' => 3,
                'purchase_price' => 80.00,
                'sale_price' => 100.00,
                'wholesale_price' => 90.00,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'tax_percent' => 0,
                'is_taxable' => false,
                'is_active' => true,
                'created_date' => now(),
            ],
            [
                'product_code' => 'P002',
                'product_name' => 'Yogurt 500g',
                'category_uid' => 2,
                'unit_uid' => 1,
                'purchase_price' => 60.00,
                'sale_price' => 80.00,
                'wholesale_price' => 70.00,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'tax_percent' => 0,
                'is_taxable' => false,
                'is_active' => true,
                'created_date' => now(),
            ],
        ];
        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }

        // Create sample customer
        DB::table('customers')->insert([
            'customer_code' => 'C001',
            'customer_name' => 'Walk-in Customer',
            'mobile_no' => '0300-0000000',
            'opening_balance' => 0,
            'advance_balance' => 0,
            'credit_limit' => 0,
            'discount_percent' => 0,
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create sample vendor
        DB::table('vendors')->insert([
            'vendor_code' => 'V001',
            'vendor_name' => 'General Supplier',
            'contact_person' => 'Supplier Admin',
            'mobile_no' => '0301-0000000',
            'opening_balance' => 0,
            'credit_limit' => 100000,
            'is_active' => true,
            'created_date' => now(),
        ]);
    }
}
