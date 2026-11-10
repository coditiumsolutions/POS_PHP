<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Branches table
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('branch_code', 50)->unique();
            $table->string('branch_name', 150);
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('mobile_no', 30)->nullable();
            $table->string('phone_no', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('branch_code');
        });

        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('user_name', 100)->unique();
            $table->string('full_name', 200);
            $table->string('password_hash', 500);
            $table->string('mobile_no', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('role_name', 50);
            $table->string('user_type', 50)->nullable();
            $table->string('terminal_name', 100)->nullable();
            $table->string('branch', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_date')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('user_name');
            $table->index('email');
        });

        // Warehouses table
        Schema::create('warehouses', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('warehouse_code', 50)->unique();
            $table->string('warehouse_name', 150);
            $table->string('location', 500)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('mobile_no', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
        });

        // Terminals table
        Schema::create('terminals', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('terminal_code', 50)->unique();
            $table->string('terminal_name', 150);
            $table->unsignedInteger('branch_uid')->nullable();
            $table->string('location', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
        });

        // Payment Methods table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('payment_method_name', 100);
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
        });

        // Product Categories table
        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('category_code', 50)->unique();
            $table->string('category_name', 150);
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
        });

        // Product Units table
        Schema::create('product_units', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('unit_code', 50)->unique();
            $table->string('unit_name', 100);
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
        });

        // Products table
        Schema::create('products', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('product_code', 50)->unique();
            $table->string('barcode', 100)->nullable();
            $table->string('product_name', 200);
            $table->unsignedInteger('category_uid')->nullable();
            $table->unsignedInteger('unit_uid')->nullable();
            $table->string('brand_name', 100)->nullable();
            $table->decimal('purchase_price', 18, 2)->default(0);
            $table->decimal('sale_price', 18, 2)->default(0);
            $table->decimal('wholesale_price', 18, 2)->default(0);
            $table->decimal('minimum_stock', 18, 3)->default(0);
            $table->decimal('maximum_stock', 18, 3)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('description', 500)->nullable();
            $table->string('image_path', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('product_code');
            $table->index('barcode');
            $table->index('product_name');
            $table->index('category_uid');
        });

        // Inventories table
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('product_uid');
            $table->unsignedInteger('warehouse_uid')->nullable();
            $table->decimal('quantity_in_stock', 18, 3)->default(0);
            $table->decimal('quantity_available', 18, 3)->default(0);
            $table->decimal('quantity_reserved', 18, 3)->default(0);
            $table->decimal('quantity_in', 18, 3)->default(0);
            $table->decimal('quantity_out', 18, 3)->default(0);
            $table->decimal('average_cost', 18, 2)->default(0);
            $table->timestamp('updated_date')->useCurrent();
            $table->unique(['product_uid', 'warehouse_uid']);
        });

        // Customers table
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('customer_code', 50)->unique();
            $table->string('customer_name', 200);
            $table->string('mobile_no', 30)->nullable();
            $table->string('phone_no', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('area', 100)->nullable();
            $table->string('ntn', 50)->nullable();
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('advance_balance', 18, 2)->default(0);
            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('customer_code');
            $table->index('customer_name');
            $table->index('mobile_no');
        });

        // Vendors table
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('vendor_code', 50)->unique();
            $table->string('vendor_name', 200);
            $table->string('contact_person', 150)->nullable();
            $table->string('mobile_no', 30)->nullable();
            $table->string('phone_no', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('ntn', 50)->nullable();
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('vendor_code');
            $table->index('vendor_name');
        });

        // Sales Invoices table
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->increments('uid');
            $table->uuid('client_transaction_id')->nullable()->unique();
            $table->string('invoice_no', 50)->unique();
            $table->timestamp('invoice_date')->useCurrent();
            $table->unsignedInteger('branch_uid')->nullable();
            $table->string('branch', 150)->nullable();
            $table->unsignedInteger('customer_uid')->nullable();
            $table->unsignedInteger('user_uid')->nullable();
            $table->string('user_name', 100)->nullable();
            $table->unsignedInteger('terminal_uid')->nullable();
            $table->string('terminal', 100)->nullable();
            $table->unsignedInteger('payment_method_uid')->nullable();
            $table->decimal('sub_total', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('other_charges', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('customer_given_amount', 18, 2)->nullable();
            $table->decimal('change_given_amount', 18, 2)->nullable();
            $table->decimal('balance_amount', 18, 2)->default(0);
            $table->string('payment_status', 30)->default('Unpaid');
            $table->string('invoice_status', 30)->default('Completed');
            $table->string('remarks', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('invoice_no');
            $table->index('invoice_date');
            $table->index('customer_uid');
            $table->index('branch_uid');
            $table->index('user_uid');
        });

        // Sales Invoice Details table
        Schema::create('sales_invoice_details', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('sales_invoice_uid');
            $table->unsignedInteger('product_uid');
            $table->decimal('quantity', 18, 3);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2);
            $table->index('sales_invoice_uid');
            $table->index('product_uid');
        });

        // Purchase Invoices table
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('purchase_invoice_no', 50)->unique();
            $table->string('vendor_invoice_no', 100)->nullable();
            $table->timestamp('purchase_date')->useCurrent();
            $table->unsignedInteger('branch_uid')->nullable();
            $table->unsignedInteger('warehouse_uid')->nullable();
            $table->unsignedInteger('vendor_uid');
            $table->unsignedInteger('user_uid')->nullable();
            $table->unsignedInteger('terminal_uid')->nullable();
            $table->decimal('sub_total', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('other_charges', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('balance_amount', 18, 2)->default(0);
            $table->string('payment_status', 30)->default('Unpaid');
            $table->string('invoice_status', 30)->default('Completed');
            $table->string('remarks', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('purchase_invoice_no');
            $table->index('purchase_date');
            $table->index('vendor_uid');
            $table->index('branch_uid');
        });

        // Purchase Invoice Details table
        Schema::create('purchase_invoice_details', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('purchase_invoice_uid');
            $table->unsignedInteger('product_uid');
            $table->decimal('quantity', 18, 3);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2);
            $table->index('purchase_invoice_uid');
            $table->index('product_uid');
        });

        // Inventory Transactions table
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('product_uid');
            $table->unsignedInteger('warehouse_uid')->nullable();
            $table->string('transaction_type', 50);
            $table->unsignedInteger('reference_uid')->nullable();
            $table->string('reference_no', 50)->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->decimal('quantity', 18, 3);
            $table->decimal('unit_cost', 18, 2)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->string('remarks', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->index('product_uid');
            $table->index('transaction_type');
            $table->index('transaction_date');
        });

        // Expense Categories table
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('category_code', 50)->unique();
            $table->string('category_name', 150);
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
        });

        // Expenses table
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('expense_no', 50)->unique();
            $table->timestamp('expense_date')->useCurrent();
            $table->unsignedInteger('expense_category_uid');
            $table->unsignedInteger('branch_uid')->nullable();
            $table->unsignedInteger('user_uid')->nullable();
            $table->decimal('amount', 18, 2);
            $table->string('payment_mode', 50)->nullable();
            $table->string('description', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('expense_date');
            $table->index('expense_category_uid');
        });

        // Stock Adjustments table
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('adjustment_no', 50)->unique();
            $table->timestamp('adjustment_date')->useCurrent();
            $table->unsignedInteger('warehouse_uid')->nullable();
            $table->unsignedInteger('user_uid')->nullable();
            $table->string('adjustment_type', 50);
            $table->string('status', 30)->default('Completed');
            $table->string('remarks', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
        });

        // Stock Adjustment Details table
        Schema::create('stock_adjustment_details', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('stock_adjustment_uid');
            $table->unsignedInteger('product_uid');
            $table->decimal('quantity', 18, 3);
            $table->string('remarks', 500)->nullable();
            $table->index('stock_adjustment_uid');
        });

        // Customer Payments table
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('payment_no', 50)->unique();
            $table->timestamp('payment_date')->useCurrent();
            $table->unsignedInteger('customer_uid');
            $table->unsignedInteger('branch_uid')->nullable();
            $table->unsignedInteger('user_uid')->nullable();
            $table->decimal('amount', 18, 2);
            $table->decimal('applied_to_invoice_amount', 18, 2)->default(0);
            $table->string('payment_mode', 50)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->string('remarks', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('customer_uid');
            $table->index('payment_date');
        });

        // Vendor Payments table
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('payment_no', 50)->unique();
            $table->timestamp('payment_date')->useCurrent();
            $table->unsignedInteger('vendor_uid');
            $table->unsignedInteger('branch_uid')->nullable();
            $table->unsignedInteger('user_uid')->nullable();
            $table->decimal('amount', 18, 2);
            $table->string('payment_mode', 50)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->string('remarks', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('vendor_uid');
            $table->index('payment_date');
        });

        // Employees table
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('employee_code', 50)->unique();
            $table->string('full_name', 200);
            $table->string('father_name', 200)->nullable();
            $table->string('cnic', 50)->nullable();
            $table->string('mobile_no', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('designation', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->timestamp('date_of_joining')->nullable();
            $table->decimal('basic_salary', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('employee_code');
        });

        // Employee Allowances table
        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('employee_uid');
            $table->string('allowance_type', 100);
            $table->decimal('amount', 18, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('employee_uid');
        });

        // Employee Deductions table
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('employee_uid');
            $table->string('deduction_type', 100);
            $table->decimal('amount', 18, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('employee_uid');
        });

        // Employee Payments table
        Schema::create('employee_payments', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('payment_no', 50)->unique();
            $table->timestamp('payment_date')->useCurrent();
            $table->unsignedInteger('employee_uid');
            $table->string('month_year', 20);
            $table->decimal('basic_salary', 18, 2)->default(0);
            $table->decimal('total_allowances', 18, 2)->default(0);
            $table->decimal('total_deductions', 18, 2)->default(0);
            $table->decimal('net_salary', 18, 2)->default(0);
            $table->string('remarks', 500)->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('employee_uid');
        });

        // Customer Monthly Supply table
        Schema::create('customer_monthly_supplies', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('customer_uid');
            $table->unsignedInteger('product_uid');
            $table->integer('day_of_month');
            $table->decimal('quantity', 18, 3);
            $table->decimal('rate', 18, 2);
            $table->decimal('delivery_charges', 18, 2)->default(0);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('customer_uid');
            $table->index('product_uid');
        });

        // Customer Monthly Supply Overrides table
        Schema::create('customer_monthly_supply_overrides', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('customer_uid');
            $table->unsignedInteger('product_uid');
            $table->date('override_date');
            $table->decimal('quantity', 18, 3);
            $table->decimal('rate', 18, 2);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_date')->nullable();
            $table->index('customer_uid');
            $table->index(['override_date', 'customer_uid'], 'idx_override_date_customer');
        });

        // Configurations table
        Schema::create('configurations', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('config_key', 100)->unique();
            $table->text('config_value')->nullable();
        });

        // Audit Logs table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('user_uid')->nullable();
            $table->string('action', 100);
            $table->string('entity_type', 100)->nullable();
            $table->unsignedInteger('entity_uid')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->timestamp('log_date')->useCurrent();
        });

        // User Sessions table
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->increments('uid');
            $table->unsignedInteger('user_uid');
            $table->string('session_token', 500);
            $table->timestamp('login_date')->useCurrent();
            $table->timestamp('logout_date')->nullable();
            $table->string('session_status', 30)->default('Active');
            $table->string('ip_address', 50)->nullable();
            $table->index('user_uid');
        });

        // Offline Sync Tables table
        Schema::create('offline_sync_tables', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('table_name', 100)->unique();
            $table->boolean('pull_from_online')->default(false);
            $table->boolean('push_to_online')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('sync_mode', 50)->default('Lookup');
        });

        // Sync Outbox table
        Schema::create('sync_outboxes', function (Blueprint $table) {
            $table->increments('uid');
            $table->string('table_name', 100);
            $table->unsignedInteger('record_uid');
            $table->string('operation', 20);
            $table->text('payload')->nullable();
            $table->string('status', 30)->default('Pending');
            $table->integer('attempt_count')->default(0);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('processed_date')->nullable();
            $table->index(['status', 'attempt_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_outboxes');
        Schema::dropIfExists('offline_sync_tables');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('configurations');
        Schema::dropIfExists('customer_monthly_supply_overrides');
        Schema::dropIfExists('customer_monthly_supplies');
        Schema::dropIfExists('employee_payments');
        Schema::dropIfExists('employee_deductions');
        Schema::dropIfExists('employee_allowances');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('vendor_payments');
        Schema::dropIfExists('customer_payments');
        Schema::dropIfExists('stock_adjustment_details');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('purchase_invoice_details');
        Schema::dropIfExists('purchase_invoices');
        Schema::dropIfExists('sales_invoice_details');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_units');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('terminals');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('users');
        Schema::dropIfExists('branches');
    }
};
