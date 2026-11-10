-- POS_PHP Database Schema
-- MySQL 8.0 Database Schema
-- Character Set: utf8mb4, Collation: utf8mb4_unicode_ci

CREATE DATABASE IF NOT EXISTS `pos_php` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pos_php`;

-- Branches Table
CREATE TABLE IF NOT EXISTS `branches` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `branch_code` varchar(50) NOT NULL,
  `branch_name` varchar(150) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `phone_no` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `branches_branch_code_unique` (`branch_code`),
  KEY `branch_code` (`branch_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `password_hash` varchar(500) NOT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `role_name` varchar(50) NOT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `terminal_name` varchar(100) DEFAULT NULL,
  `branch` varchar(150) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_date` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `users_user_name_unique` (`user_name`),
  KEY `user_name` (`user_name`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Warehouses Table
CREATE TABLE IF NOT EXISTS `warehouses` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_code` varchar(50) NOT NULL,
  `warehouse_name` varchar(150) NOT NULL,
  `location` varchar(500) DEFAULT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `warehouses_warehouse_code_unique` (`warehouse_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Terminals Table
CREATE TABLE IF NOT EXISTS `terminals` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `terminal_code` varchar(50) NOT NULL,
  `terminal_name` varchar(150) NOT NULL,
  `branch_uid` int unsigned DEFAULT NULL,
  `location` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `terminals_terminal_code_unique` (`terminal_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment Methods Table
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_method_name` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Categories Table
CREATE TABLE IF NOT EXISTS `product_categories` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(50) NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `product_categories_category_code_unique` (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Units Table
CREATE TABLE IF NOT EXISTS `product_units` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `unit_code` varchar(50) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `product_units_unit_code_unique` (`unit_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `product_code` varchar(50) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `category_uid` int unsigned DEFAULT NULL,
  `unit_uid` int unsigned DEFAULT NULL,
  `brand_name` varchar(100) DEFAULT NULL,
  `purchase_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `wholesale_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `minimum_stock` decimal(18,3) NOT NULL DEFAULT '0.000',
  `maximum_stock` decimal(18,3) NOT NULL DEFAULT '0.000',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_taxable` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` varchar(500) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `products_product_code_unique` (`product_code`),
  KEY `product_code` (`product_code`),
  KEY `barcode` (`barcode`),
  KEY `product_name` (`product_name`),
  KEY `category_uid` (`category_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inventories Table
CREATE TABLE IF NOT EXISTS `inventories` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `product_uid` int unsigned NOT NULL,
  `warehouse_uid` int unsigned DEFAULT NULL,
  `quantity_in_stock` decimal(18,3) NOT NULL DEFAULT '0.000',
  `quantity_available` decimal(18,3) NOT NULL DEFAULT '0.000',
  `quantity_reserved` decimal(18,3) NOT NULL DEFAULT '0.000',
  `quantity_in` decimal(18,3) NOT NULL DEFAULT '0.000',
  `quantity_out` decimal(18,3) NOT NULL DEFAULT '0.000',
  `average_cost` decimal(18,2) NOT NULL DEFAULT '0.00',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `inventories_product_uid_warehouse_uid_unique` (`product_uid`,`warehouse_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customers Table
CREATE TABLE IF NOT EXISTS `customers` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(50) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `phone_no` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `ntn` varchar(50) DEFAULT NULL,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT '0.00',
  `advance_balance` decimal(18,2) NOT NULL DEFAULT '0.00',
  `credit_limit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `customers_customer_code_unique` (`customer_code`),
  KEY `customer_code` (`customer_code`),
  KEY `customer_name` (`customer_name`),
  KEY `mobile_no` (`mobile_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vendors Table
CREATE TABLE IF NOT EXISTS `vendors` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `vendor_code` varchar(50) NOT NULL,
  `vendor_name` varchar(200) NOT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `phone_no` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `ntn` varchar(50) DEFAULT NULL,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT '0.00',
  `credit_limit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `vendors_vendor_code_unique` (`vendor_code`),
  KEY `vendor_code` (`vendor_code`),
  KEY `vendor_name` (`vendor_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales Invoices Table
CREATE TABLE IF NOT EXISTS `sales_invoices` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `client_transaction_id` char(36) DEFAULT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `invoice_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `branch_uid` int unsigned DEFAULT NULL,
  `branch` varchar(150) DEFAULT NULL,
  `customer_uid` int unsigned DEFAULT NULL,
  `user_uid` int unsigned DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `terminal_uid` int unsigned DEFAULT NULL,
  `terminal` varchar(100) DEFAULT NULL,
  `payment_method_uid` int unsigned DEFAULT NULL,
  `sub_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `other_charges` decimal(18,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `customer_given_amount` decimal(18,2) DEFAULT NULL,
  `change_given_amount` decimal(18,2) DEFAULT NULL,
  `balance_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `payment_status` varchar(30) NOT NULL DEFAULT 'Unpaid',
  `invoice_status` varchar(30) NOT NULL DEFAULT 'Completed',
  `pos_type` varchar(30) NOT NULL DEFAULT 'Walk-In',
  `remarks` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `sales_invoices_invoice_no_unique` (`invoice_no`),
  KEY `sales_invoices_pos_type_index` (`pos_type`),
  UNIQUE KEY `sales_invoices_client_transaction_id_unique` (`client_transaction_id`),
  KEY `invoice_no` (`invoice_no`),
  KEY `invoice_date` (`invoice_date`),
  KEY `customer_uid` (`customer_uid`),
  KEY `branch_uid` (`branch_uid`),
  KEY `user_uid` (`user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales Invoice Details Table
CREATE TABLE IF NOT EXISTS `sales_invoice_details` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `sales_invoice_uid` int unsigned NOT NULL,
  `product_uid` int unsigned NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(18,2) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `sales_invoice_uid` (`sales_invoice_uid`),
  KEY `product_uid` (`product_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchase Invoices Table
CREATE TABLE IF NOT EXISTS `purchase_invoices` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `purchase_invoice_no` varchar(50) NOT NULL,
  `vendor_invoice_no` varchar(100) DEFAULT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `branch_uid` int unsigned DEFAULT NULL,
  `warehouse_uid` int unsigned DEFAULT NULL,
  `vendor_uid` int unsigned NOT NULL,
  `user_uid` int unsigned DEFAULT NULL,
  `terminal_uid` int unsigned DEFAULT NULL,
  `sub_total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `other_charges` decimal(18,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `balance_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `payment_status` varchar(30) NOT NULL DEFAULT 'Unpaid',
  `invoice_status` varchar(30) NOT NULL DEFAULT 'Completed',
  `remarks` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `purchase_invoices_purchase_invoice_no_unique` (`purchase_invoice_no`),
  KEY `purchase_invoice_no` (`purchase_invoice_no`),
  KEY `purchase_date` (`purchase_date`),
  KEY `vendor_uid` (`vendor_uid`),
  KEY `branch_uid` (`branch_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchase Invoice Details Table
CREATE TABLE IF NOT EXISTS `purchase_invoice_details` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `purchase_invoice_uid` int unsigned NOT NULL,
  `product_uid` int unsigned NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(18,2) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `purchase_invoice_uid` (`purchase_invoice_uid`),
  KEY `product_uid` (`product_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inventory Transactions Table
CREATE TABLE IF NOT EXISTS `inventory_transactions` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `product_uid` int unsigned NOT NULL,
  `warehouse_uid` int unsigned DEFAULT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `reference_uid` int unsigned DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `quantity` decimal(18,3) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(18,2) NOT NULL DEFAULT '0.00',
  `remarks` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  KEY `product_uid` (`product_uid`),
  KEY `transaction_type` (`transaction_type`),
  KEY `transaction_date` (`transaction_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Expense Categories Table
CREATE TABLE IF NOT EXISTS `expense_categories` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(50) NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `expense_categories_category_code_unique` (`category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Expenses Table
CREATE TABLE IF NOT EXISTS `expenses` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `expense_no` varchar(50) NOT NULL,
  `expense_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expense_category_uid` int unsigned NOT NULL,
  `branch_uid` int unsigned DEFAULT NULL,
  `user_uid` int unsigned DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `expenses_expense_no_unique` (`expense_no`),
  KEY `expense_date` (`expense_date`),
  KEY `expense_category_uid` (`expense_category_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Adjustments Table
CREATE TABLE IF NOT EXISTS `stock_adjustments` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_no` varchar(50) NOT NULL,
  `adjustment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `warehouse_uid` int unsigned DEFAULT NULL,
  `user_uid` int unsigned DEFAULT NULL,
  `adjustment_type` varchar(50) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Completed',
  `remarks` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `stock_adjustments_adjustment_no_unique` (`adjustment_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Adjustment Details Table
CREATE TABLE IF NOT EXISTS `stock_adjustment_details` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `stock_adjustment_uid` int unsigned NOT NULL,
  `product_uid` int unsigned NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `stock_adjustment_uid` (`stock_adjustment_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer Payments Table
CREATE TABLE IF NOT EXISTS `customer_payments` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(50) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_uid` int unsigned NOT NULL,
  `branch_uid` int unsigned DEFAULT NULL,
  `user_uid` int unsigned DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `applied_to_invoice_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `payment_mode` varchar(50) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `customer_payments_payment_no_unique` (`payment_no`),
  KEY `customer_uid` (`customer_uid`),
  KEY `payment_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vendor Payments Table
CREATE TABLE IF NOT EXISTS `vendor_payments` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(50) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vendor_uid` int unsigned NOT NULL,
  `branch_uid` int unsigned DEFAULT NULL,
  `user_uid` int unsigned DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `vendor_payments_payment_no_unique` (`payment_no`),
  KEY `vendor_uid` (`vendor_uid`),
  KEY `payment_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employees Table
CREATE TABLE IF NOT EXISTS `employees` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(50) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `father_name` varchar(200) DEFAULT NULL,
  `cnic` varchar(50) DEFAULT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `date_of_joining` timestamp NULL DEFAULT NULL,
  `basic_salary` decimal(18,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  KEY `employee_code` (`employee_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employee Allowances Table
CREATE TABLE IF NOT EXISTS `employee_allowances` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `employee_uid` int unsigned NOT NULL,
  `allowance_type` varchar(100) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `employee_uid` (`employee_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employee Deductions Table
CREATE TABLE IF NOT EXISTS `employee_deductions` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `employee_uid` int unsigned NOT NULL,
  `deduction_type` varchar(100) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `employee_uid` (`employee_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employee Payments Table
CREATE TABLE IF NOT EXISTS `employee_payments` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(50) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `employee_uid` int unsigned NOT NULL,
  `month_year` varchar(20) NOT NULL,
  `basic_salary` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_allowances` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(18,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(18,2) NOT NULL DEFAULT '0.00',
  `remarks` varchar(500) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `employee_payments_payment_no_unique` (`payment_no`),
  KEY `employee_uid` (`employee_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer Monthly Supply Table
CREATE TABLE IF NOT EXISTS `customer_monthly_supplies` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_uid` int unsigned NOT NULL,
  `product_uid` int unsigned NOT NULL,
  `day_of_month` int NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `rate` decimal(18,2) NOT NULL,
  `delivery_charges` decimal(18,2) NOT NULL DEFAULT '0.00',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `customer_uid` (`customer_uid`),
  KEY `product_uid` (`product_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer Monthly Supply Overrides Table
CREATE TABLE IF NOT EXISTS `customer_monthly_supply_overrides` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_uid` int unsigned NOT NULL,
  `product_uid` int unsigned NOT NULL,
  `override_date` date NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `rate` decimal(18,2) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `customer_uid` (`customer_uid`),
  KEY `idx_override_date_customer` (`override_date`,`customer_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configurations Table
CREATE TABLE IF NOT EXISTS `configurations` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL,
  `config_value` text,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `configurations_config_key_unique` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Logs Table
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `user_uid` int unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_uid` int unsigned DEFAULT NULL,
  `description` text,
  `ip_address` varchar(50) DEFAULT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Sessions Table
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `user_uid` int unsigned NOT NULL,
  `session_token` varchar(500) NOT NULL,
  `login_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logout_date` timestamp NULL DEFAULT NULL,
  `session_status` varchar(30) NOT NULL DEFAULT 'Active',
  `ip_address` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `user_uid` (`user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Offline Sync Tables Table
CREATE TABLE IF NOT EXISTS `offline_sync_tables` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `pull_from_online` tinyint(1) NOT NULL DEFAULT '0',
  `push_to_online` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sync_mode` varchar(50) NOT NULL DEFAULT 'Lookup',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `offline_sync_tables_table_name_unique` (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sync Outbox Table
CREATE TABLE IF NOT EXISTS `sync_outboxes` (
  `uid` int unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `record_uid` int unsigned NOT NULL,
  `operation` varchar(20) NOT NULL,
  `payload` text,
  `status` varchar(30) NOT NULL DEFAULT 'Pending',
  `attempt_count` int NOT NULL DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `sync_outboxes_status_attempt_count_index` (`status`,`attempt_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Personal Access Tokens (Laravel Default)
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrations Table (Laravel System)
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
