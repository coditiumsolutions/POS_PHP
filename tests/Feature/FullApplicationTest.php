<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Branch;
use App\Models\ProductCategory;
use App\Models\Warehouse;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FullApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::create([
            'user_name' => 'admin',
            'full_name' => 'Admin User',
            'password_hash' => bcrypt('admin123'),
            'role_name' => 'Administrator',
            'is_active' => true,
            'created_date' => now(),
        ]);
        
        // Seed test data
        $this->seedTestData();
    }

    protected function seedTestData()
    {
        // Create product category
        $category = ProductCategory::create([
            'category_code' => 'CAT001',
            'category_name' => 'Test Category',
            'is_active' => true,
        ]);

        // Create products
        for ($i = 1; $i <= 3; $i++) {
            Product::create([
                'product_code' => "PROD{$i}",
                'product_name' => "Test Product {$i}",
                'barcode' => "BAR{$i}",
                'unit_price' => 10.00 * $i,
                'sale_price' => 15.00 * $i,
                'category_uid' => $category->uid,
                'is_active' => true,
                'created_date' => now(),
            ]);
        }

        // Create customer
        Customer::create([
            'customer_code' => 'CUST001',
            'customer_name' => 'Test Customer',
            'mobile_no' => '03001234567',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create vendor
        Vendor::create([
            'vendor_code' => 'VEND001',
            'vendor_name' => 'Test Vendor',
            'mobile_no' => '03009876543',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create branch
        Branch::create([
            'branch_code' => 'BR001',
            'branch_name' => 'Test Branch',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create warehouse
        Warehouse::create([
            'warehouse_code' => 'WH01',
            'warehouse_name' => 'Test Warehouse',
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create employee
        Employee::create([
            'employee_code' => 'EMP001',
            'full_name' => 'Test Employee',
            'basic_salary' => 50000.00,
            'is_active' => true,
            'created_date' => now(),
        ]);

        // Create expense category
        ExpenseCategory::create([
            'category_code' => 'EXPCAT001',
            'category_name' => 'Test Expense Category',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function test_authentication_and_dashboard()
    {
        // Test login page loads
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login');

        // Test login works
        $response = $this->post('/login', [
            'user_name' => 'admin',
            'password' => 'admin123',
        ]);
        $response->assertRedirect('/dashboard');

        // Test dashboard loads
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_all_main_index_pages_load()
    {
        $routes = [
            '/dashboard',
            '/pos/cash',
            '/products',
            '/customers',
            '/vendors',
            '/sales-invoices',
            '/purchase-invoices',
            '/sales-returns',
            '/customer-payments',
            '/vendor-payments',
            '/stock-adjustments',
            '/wastage',
            '/expenses',
            '/inventories',
            '/employees',
            '/monthly-supply',
            '/yogurt-production',
            '/reports',
            '/reports/sales',
            '/reports/purchases',
            '/reports/expenses',
            '/reports/inventory',
            '/reports/branch',
            '/reports/user',
            '/users',
            '/branches',
            '/product-categories',
            '/expense-categories',
            '/warehouses',
            '/audit-logs',
            '/configurations',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertNotEquals(500, $response->status(), "Route {$route} returned 500 error");
            $this->assertNotEquals(404, $response->status(), "Route {$route} returned 404 error");
        }
    }

    /** @test */
    public function test_all_create_pages_load()
    {
        $createRoutes = [
            '/products/create',
            '/customers/create',
            '/vendors/create',
            '/sales-invoices/create',
            '/purchase-invoices/create',
            '/sales-returns/create',
            '/customer-payments/create',
            '/vendor-payments/create',
            '/stock-adjustments/create',
            '/wastage/create',
            '/expenses/create',
            '/employees/create',
            '/monthly-supply/create',
            '/yogurt-production/create',
            '/users/create',
            '/branches/create',
            '/product-categories/create',
            '/expense-categories/create',
            '/warehouses/create',
            '/configurations/create',
        ];

        foreach ($createRoutes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertNotEquals(500, $response->status(), "Create route {$route} returned 500 error");
            $this->assertNotEquals(404, $response->status(), "Create route {$route} returned 404 error");
        }
    }

    /** @test */
    public function test_edit_pages_load_with_real_ids()
    {
        $product = Product::first();
        $customer = Customer::first();
        $vendor = Vendor::first();
        $branch = Branch::first();
        $employee = Employee::first();

        $editRoutes = [
            "/products/{$product->uid}/edit",
            "/customers/{$customer->uid}/edit",
            "/vendors/{$vendor->uid}/edit",
            "/branches/{$branch->uid}/edit",
            "/employees/{$employee->uid}/edit",
            "/users/{$this->admin->uid}/edit",
        ];

        foreach ($editRoutes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertNotEquals(500, $response->status(), "Edit route {$route} returned 500 error");
            $this->assertNotEquals(404, $response->status(), "Edit route {$route} returned 404 error");
        }
    }

    /** @test */
    public function test_can_create_product()
    {
        $response = $this->actingAs($this->admin)->post('/products', [
            'product_code' => 'NEWPROD',
            'product_name' => 'New Product',
            'unit_price' => 100.00,
            'sale_price' => 150.00,
            'barcode' => 'NEWBAR',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'product_code' => 'NEWPROD',
            'product_name' => 'New Product',
        ]);
    }

    /** @test */
    public function test_can_create_customer()
    {
        $response = $this->actingAs($this->admin)->post('/customers', [
            'customer_code' => 'NEWCUST',
            'customer_name' => 'New Customer',
            'mobile_no' => '03001111111',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'customer_code' => 'NEWCUST',
            'customer_name' => 'New Customer',
        ]);
    }

    /** @test */
    public function test_can_create_vendor()
    {
        $response = $this->actingAs($this->admin)->post('/vendors', [
            'vendor_code' => 'NEWVEND',
            'vendor_name' => 'New Vendor',
            'mobile_no' => '03002222222',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vendors', [
            'vendor_code' => 'NEWVEND',
            'vendor_name' => 'New Vendor',
        ]);
    }

    /** @test */
    public function test_can_create_branch()
    {
        $response = $this->actingAs($this->admin)->post('/branches', [
            'branch_name' => 'New Branch',
            'city' => 'Test City',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', [
            'branch_name' => 'New Branch',
        ]);
    }

    /** @test */
    public function test_can_create_user()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'user_name' => 'newuser',
            'full_name' => 'New User',
            'password' => 'password123',
            'role_name' => 'Cashier',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'user_name' => 'newuser',
            'full_name' => 'New User',
        ]);
    }

    /** @test */
    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_all_routes_require_authentication()
    {
        $protectedRoutes = [
            '/dashboard',
            '/pos/cash',
            '/products',
            '/customers',
            '/sales-invoices',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }
}
