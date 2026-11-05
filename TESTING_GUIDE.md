# POS_PHP Testing Guide

## Quick Start Testing

### 1. Access the Application
```
URL: http://127.0.0.1:8042
Default Admin: admin / admin123
```

### 2. Test Core Workflows

#### A. Complete Sales Flow
1. **Login** → Dashboard
2. **POS** → Select products → Add to cart → Checkout
3. **Sales Invoices** → View created invoice → Print
4. **Sales Returns** → Create return for invoice → Verify stock restored
5. **Customer Payments** → Record payment → Allocate to invoice

#### B. Purchase & Inventory Flow
1. **Purchase Invoices** → Create purchase → Verify stock increases
2. **Vendors** → Add vendor
3. **Vendor Payments** → Record payment against purchase
4. **Stock Adjustments** → Physical count adjustment
5. **Wastage** → Record wastage → Verify stock decreases

#### C. Specialized Modules
1. **Yogurt Production**
   - Navigate to Yogurt Production
   - Enter milk quantity (e.g., 10 kg)
   - System calculates yogurt output (9.5 kg with 5% loss)
   - Submit → Verify milk stock decreased and yogurt stock increased
   
2. **Monthly Supply**
   - Create template: Customer → Product → Day of Month → Quantity
   - Set generation date to match day of month
   - Click "Generate Invoices"
   - Verify sales invoices created automatically

3. **Employee Payroll**
   - Add employee with basic salary
   - Add allowances/deductions
   - Go to Payroll tab
   - Calculate net salary
   - Record payment

#### D. Admin & Configuration
1. **Users** → Create new user with role
2. **Branches** → Add branch
3. **Product Categories** → Add category
4. **Warehouses** → Add warehouse
5. **Configuration** → Update shop name/settings
6. **Audit Logs** → View activity log

### 3. Test Each Module

| Module | Test Case | Expected Result |
|--------|-----------|-----------------|
| **Login** | Enter admin/admin123 | Redirect to dashboard |
| **Dashboard** | View metrics | Shows sales, stock alerts |
| **POS** | Create sale | Invoice generated, stock reduced |
| **Sales Invoice** | List invoices | Paginated list with filters |
| **Sales Returns** | Return items | Stock added back, return invoice created |
| **Purchase Invoice** | Create purchase | Stock increased, cost updated |
| **Customer Payments** | Record ₨1000 payment | Applied to invoice, balance reduced |
| **Vendor Payments** | Record payment | Applied to purchase invoice |
| **Stock Adjustment** | Physical count | Stock corrected, transaction logged |
| **Wastage** | Record expired items | Stock reduced, wastage logged |
| **Products** | Add/edit product | Saved with price, category |
| **Customers** | Add customer | Saved with contact details |
| **Vendors** | Add vendor | Saved with payment terms |
| **Employees** | Add employee | Saved with salary details |
| **Payroll** | Calculate salary | Basic + Allowances - Deductions |
| **Monthly Supply** | Generate invoices | Invoices created for templates |
| **Yogurt Production** | Record production | Milk consumed, yogurt produced |
| **Reports** | Sales report | Shows filtered data |
| **Expenses** | Add expense | Expense logged with category |
| **Inventory** | View stock levels | Current stock by product |
| **Users** | Create user (Admin) | User created with role |
| **Branches** | Add branch | Branch saved with code |
| **Configuration** | Update settings | Settings saved |
| **Audit Logs** | View logs | User actions displayed |

## Detailed Test Scenarios

### Scenario 1: Daily POS Operations
```
1. Login as Cashier
2. POS → Select 5 products
3. Apply 10% discount on 2 items
4. Customer pays ₨1000, sale is ₨850
5. System shows ₨150 change
6. Complete sale → Print invoice
7. Check inventory → Stock reduced for all 5 products
```

### Scenario 2: Return Processing
```
1. Sales Invoices → Find invoice
2. Click "Return" or Sales Returns → Create
3. Select original invoice
4. Select items to return (full or partial)
5. Submit return
6. Verify: Return invoice created
7. Verify: Stock restored to inventory
8. Verify: Inventory transactions logged
```

### Scenario 3: Payment Reconciliation
```
1. Customer owes ₨5000 across 3 invoices
2. Customer Payments → New Payment
3. Enter ₨5000
4. Allocate ₨2000 to invoice #1 (fully paid)
5. Allocate ₨2000 to invoice #2 (fully paid)
6. Allocate ₨1000 to invoice #3 (partial)
7. Submit payment
8. Verify: Invoice #1 and #2 marked "Paid"
9. Verify: Invoice #3 balance reduced to remaining amount
```

### Scenario 4: Stock Adjustment
```
1. Physical stock count shows 100 units
2. System shows 105 units
3. Stock Adjustments → Create
4. Type: Physical Count
5. Product: Select product
6. Quantity: 100
7. Remarks: "Weekly physical verification"
8. Submit
9. Verify: Stock updated to 100
10. Verify: Transaction logged as -5 adjustment
```

### Scenario 5: Yogurt Production Workflow
```
1. Current Stock: Milk = 50 kg, Yogurt = 0 kg
2. Yogurt Production → Create
3. Milk Quantity: 20 kg
4. System calculates: Yogurt Output = 19 kg, Loss = 1 kg
5. Submit
6. Verify Final Stock:
   - Milk = 30 kg (50 - 20)
   - Yogurt = 19 kg (0 + 19)
7. Inventory Transactions:
   - Milk: -20 kg (Yogurt Production)
   - Yogurt: +19 kg (Yogurt Production)
   - Loss: 1 kg logged
```

### Scenario 6: Monthly Supply Automation
```
1. Setup:
   - Customer: ABC Shop
   - Product: Milk 1L
   - Day of Month: 5
   - Default Quantity: 10 units
   
2. On 5th of month:
   - Monthly Supply → Generate Invoices
   - Date: 2026-11-05
   - Click "Generate"
   
3. Result:
   - 1 sales invoice created
   - Customer: ABC Shop
   - 1 line item: Milk 1L × 10
   - Status: Completed
   - Payment Status: Pending
```

## Performance Testing

### Load Test Checklist
- [ ] 50+ products in catalog
- [ ] 100+ sales invoices
- [ ] 50+ customers
- [ ] 20+ vendors
- [ ] Multiple concurrent users (if possible)
- [ ] Large date range reports
- [ ] Invoice printing performance

### Expected Performance
- POS checkout: < 2 seconds
- Invoice listing (50 records): < 1 second
- Report generation: < 5 seconds
- Stock update: < 1 second

## Browser Testing
- [ ] Chrome/Edge (primary)
- [ ] Firefox
- [ ] Safari (if on Mac)
- [ ] Mobile responsive (optional)

## Security Testing
- [ ] Login with wrong credentials → Error
- [ ] Access admin routes as Cashier → Redirect
- [ ] Logout → Session cleared
- [ ] Direct URL access without login → Redirect to login

## Data Integrity Checks

After performing operations, verify:
1. **Inventory Transactions Table**: Every stock change has a transaction
2. **Invoice Totals**: Sub-total + Tax - Discount = Net Amount
3. **Payment Balances**: Paid Amount + Balance = Net Amount
4. **Stock Levels**: quantity_in_stock = quantity_available (unless reserved)

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| "Call to undefined method User::isAdmin()" | Update User model to add isAdmin() method |
| 404 on routes | Clear route cache: `php artisan route:clear` |
| CSRF token mismatch | Clear browser cookies, re-login |
| Database connection error | Check .env database credentials |
| Stock not updating | Check inventory_transactions table for logs |

## Test Data Seeding

```bash
# Create sample data for testing
php artisan db:seed --class=TestDataSeeder
```

Or manually create:
- 5-10 products
- 3-5 customers
- 2-3 vendors
- 1-2 expense categories
- 1 warehouse
- 1 branch

## Reporting Issues

When reporting issues, include:
1. **Steps to reproduce**
2. **Expected result**
3. **Actual result**
4. **User role** (Admin/Manager/Cashier)
5. **Browser** and version
6. **Error message** (if any)
7. **Screenshot** (if applicable)

## Test Completion Checklist

- [ ] All modules accessible from navigation
- [ ] Login/Logout works
- [ ] POS sale completes successfully
- [ ] Sales invoice created and printable
- [ ] Sales return processes correctly
- [ ] Stock levels update correctly
- [ ] Purchase invoice increases stock
- [ ] Customer payment allocates to invoice
- [ ] Vendor payment records correctly
- [ ] Stock adjustment updates inventory
- [ ] Wastage reduces stock
- [ ] Yogurt production works (milk → yogurt)
- [ ] Monthly supply generates invoices
- [ ] Employee payroll calculates correctly
- [ ] Reports display data
- [ ] Admin can create users
- [ ] Configuration saves settings
- [ ] Audit logs capture actions
- [ ] All CRUD operations work (Create, Read, Update, Delete)
- [ ] Navigation between modules works
- [ ] Session persists across pages
- [ ] Role-based access enforced

## Final Sign-off

When all tests pass:
- ✅ **System is production-ready**
- ✅ **All POS_MT features ported**
- ✅ **Data integrity maintained**
- ✅ **User roles working**
- ✅ **Documentation complete**

---

**Testing completed by:** ________________  
**Date:** ________________  
**Status:** ________________  
