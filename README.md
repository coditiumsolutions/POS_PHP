# POS_PHP - Point of Sale System

A complete port of POS_MT (ASP.NET Core MVC) to PHP using Laravel 10 framework with MySQL 8.

## Overview

POS_PHP is a comprehensive point-of-sale system that includes:

- **POS Sales Screen** - Quick cash sales with product search, categories, and cart management
- **Sales Invoices** - Complete sales invoice management with customer tracking
- **Purchase Invoices** - Vendor purchase management and inventory updates
- **Customers & Vendors** - Full customer and vendor management
- **Products & Inventory** - Product catalog with category management and stock tracking
- **Expenses** - Expense tracking with categories
- **Reports** - Sales, purchase, expense, and inventory reports
- **Users & Branches** - Multi-user system with role-based access control
- **Employee Management** - Employee records with payroll tracking
- **Monthly Supply** - Recurring customer supply scheduling
- **Stock Adjustments** - Inventory adjustment and stock movement tracking

## Technology Stack

**Framework Choice: Laravel 10**

Laravel was selected for this port because:
- Industry-standard PHP framework with excellent documentation
- Built-in authentication and authorization system
- Eloquent ORM for database operations (similar to Entity Framework)
- Blade templating engine (similar to Razor)
- Strong community support and package ecosystem
- Works seamlessly on both Windows (XAMPP/Laragon) and Linux

**Stack:**
- PHP 8.3+
- MySQL 8.0
- Laravel 10.x
- Bootstrap 5.3
- jQuery 3.7

## System Requirements

- PHP 8.3 or higher
- MySQL 8.0 or higher
- Composer
- Web server (Apache/Nginx) or PHP built-in server

### PHP Extensions Required:
- php-mysql
- php-mbstring
- php-xml
- php-curl
- php-zip
- php-bcmath
- php-intl
- php-gd

## Installation

### On Windows (XAMPP/Laragon)

1. **Install XAMPP or Laragon** with PHP 8.3+ and MySQL 8.0+

2. **Clone or extract this project** to your web root:
   ```
   C:\xampp\htdocs\pos_php\
   ```
   or
   ```
   C:\laragon\www\pos_php\
   ```

3. **Install Composer** if not already installed:
   - Download from https://getcomposer.org/download/

4. **Install dependencies:**
   ```bash
   cd pos_php
   composer install
   ```

5. **Configure database:**
   - Start MySQL from XAMPP/Laragon control panel
   - Create database `pos_php` in phpMyAdmin
   - Copy `.env.example` to `.env`
   - Edit `.env` and set database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pos_php
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

7. **Run migrations and seed data:**
   ```bash
   php artisan migrate:fresh --seed
   ```

8. **Start the development server:**
   ```bash
   php artisan serve
   ```
   or configure Apache virtual host

9. **Access the application:**
   - URL: http://localhost:8000 (or your configured URL)
   - Username: `admin`
   - Password: `admin123`

### On Linux (Ubuntu/Debian)

1. **Install dependencies:**
   ```bash
   sudo apt update
   sudo apt install php8.3 php8.3-cli php8.3-common php8.3-mysql \
                    php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl \
                    php8.3-xml php8.3-bcmath php8.3-intl \
                    mysql-server composer
   ```

2. **Clone the repository:**
   ```bash
   git clone <repository-url> pos_php
   cd pos_php
   ```

3. **Install dependencies:**
   ```bash
   composer install
   ```

4. **Configure MySQL:**
   ```bash
   sudo mysql
   ```
   ```sql
   CREATE DATABASE pos_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'pos_user'@'localhost' IDENTIFIED BY 'pos_password';
   GRANT ALL PRIVILEGES ON pos_php.* TO 'pos_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

5. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` and update database credentials

6. **Run migrations and seed:**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Set permissions:**
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

8. **Start server:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

## Default Users

The system seeds three default users:

| Username | Password   | Role          | Description                |
|----------|------------|---------------|----------------------------|
| admin    | admin123   | Administrator | Full system access         |
| manager  | manager123 | Manager       | Sales, inventory, reports  |
| cashier  | cashier123 | Cashier       | POS and basic sales only   |

**⚠️ IMPORTANT:** Change these passwords immediately after first login in production!

## Features Overview

### 1. POS (Point of Sale)
- **Cash Sale Screen** - Quick sale interface with product search
- Product categories for quick filtering
- Real-time cart management
- Multiple payment methods
- Customer selection (walk-in or registered)
- Cash management with change calculation
- Invoice printing

### 2. Sales Management
- Sales invoice listing with search and filters
- View invoice details
- Print invoices
- Sales returns processing
- Customer payment tracking

### 3. Purchase Management
- Purchase invoice creation
- Vendor management
- Automatic inventory updates
- Vendor payment tracking

### 4. Inventory Management
- Real-time stock tracking
- Product catalog with categories
- Stock adjustments
- Inventory transactions log
- Low stock alerts
- Product barcode support

### 5. Customer & Vendor Management
- Customer database with contact details
- Credit limit tracking
- Advance balance management
- Vendor database
- Purchase and payment history

### 6. Expenses
- Expense recording with categories
- Branch-wise expense tracking
- Expense reports by date range

### 7. Reports
- Sales reports (daily, monthly, by customer, by user)
- Purchase reports
- Expense reports
- Inventory reports
- Low stock report
- Branch-wise reports
- User activity reports

### 8. User Management (Admin)
- User creation and management
- Role-based access control (Admin, Manager, Cashier, Viewer)
- Branch assignment
- Terminal assignment

### 9. System Configuration
- Branch management
- Warehouse management
- Terminal configuration
- Product categories and units
- Expense categories
- Payment methods

## Database Schema

The system uses 30+ tables covering:
- Users and authentication
- Products, categories, and inventory
- Customers and vendors
- Sales and purchase invoices with details
- Payments and expenses
- Employees and payroll
- Stock adjustments
- Audit logs

See `docs/PORTING.md` for complete schema mapping from SQL Server to MySQL.

## Key Differences from Original (POS_MT)

1. **Framework**: Laravel instead of ASP.NET Core MVC
2. **Database**: MySQL 8 instead of SQL Server
3. **ORM**: Eloquent instead of Entity Framework Core
4. **Views**: Blade templates instead of Razor (.cshtml)
5. **Authentication**: Laravel's built-in auth instead of ASP.NET Identity
6. **Primary Keys**: Using `uid` (int) as primary key for compatibility
7. **Timestamps**: Using `created_date` and `updated_date` instead of Laravel conventions

## Project Structure

```
pos_php/
├── app/
│   ├── Http/Controllers/    # All application controllers
│   ├── Models/               # Eloquent models (30+ models)
│   └── ...
├── database/
│   ├── migrations/           # Database migration files
│   └── seeders/              # Database seeders with initial data
├── resources/
│   └── views/                # Blade templates
│       ├── layouts/          # Layout templates
│       ├── auth/             # Login/authentication views
│       ├── pos/              # POS screen views
│       ├── dashboard/        # Dashboard views
│       ├── products/         # Product management views
│       ├── customers/        # Customer management views
│       └── ...
├── routes/
│   └── web.php              # Application routes
├── public/                  # Public assets
├── storage/                 # File storage, logs, cache
├── docs/                    # Documentation
│   └── PORTING.md          # Detailed porting documentation
├── .env.example            # Environment configuration template
└── README.md               # This file
```

## Testing

### Manual Testing Checklist

The following flows have been tested and verified:

1. **Login Flow**
   - Login with admin credentials
   - Authentication and session management
   - Role-based dashboard access

2. **POS Flow**
   - Open POS cash screen
   - Search and add products to cart
   - Select customer (optional)
   - Complete sale with payment
   - Print invoice

3. **Product Management**
   - List products with search
   - Create new product
   - Edit existing product
   - Category assignment

4. **Customer Management**
   - List customers
   - Create new customer
   - Edit customer details
   - Search functionality

5. **Inventory**
   - View stock levels
   - Automatic stock updates after sales
   - Inventory transactions log

6. **Reports**
   - Sales summary reports
   - Inventory status
   - Low stock alerts

## Troubleshooting

### Database Connection Issues
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database exists: `php artisan migrate:status`

### Permission Issues (Linux)
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Blank Page / 500 Error
- Check Laravel logs: `storage/logs/laravel.log`
- Verify `.env` file exists and has correct settings
- Run: `php artisan config:clear && php artisan cache:clear`

### Migration Errors
- Drop all tables and re-run: `php artisan migrate:fresh --seed`
- Or manually reset: Drop database, recreate, then migrate

## Development

### Adding New Features
1. Create model: `php artisan make:model ModelName`
2. Create controller: `php artisan make:controller ControllerName`
3. Add routes in `routes/web.php`
4. Create views in `resources/views/`

### Database Changes
1. Create migration: `php artisan make:migration description_of_change`
2. Edit migration file in `database/migrations/`
3. Run: `php artisan migrate`

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Generate new `APP_KEY`: `php artisan key:generate`
4. Optimize: 
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Set proper file permissions
6. Configure web server (Apache/Nginx)
7. Enable HTTPS
8. Set up regular database backups
9. Change all default passwords

## Security Notes

- All routes are protected with authentication middleware
- CSRF protection enabled on all forms
- Password hashing using bcrypt
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating
- Role-based access control for admin functions

## Support & Documentation

- Full porting documentation: `docs/PORTING.md`
- Laravel documentation: https://laravel.com/docs/10.x
- Report issues in the project repository

## License

This is a port of POS_MT. Check the original project for license information.

## Credits

- Original POS_MT: ASP.NET Core application
- Ported to Laravel by POS_PHP development team
- Built with Laravel 10, Bootstrap 5, and jQuery
