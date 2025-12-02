# Sales Management System

Aplikasi manajemen penjualan berbasis web yang dibangun dengan Laravel 11, dilengkapi dengan sistem role & permission, split payment, dan auto-generated codes.

## 🚀 Fitur Utama

### 📊 Dashboard
- Grafik penjualan bulanan (Chart.js)
- Statistik item terlaris (Pie chart)
- Recent sales summary
- Quick access navigation

### 🛍️ Sales Management
- CRUD sales dengan multiple items
- Auto-generated sales code (SL-YYYYMMDD-XXXX)
- Searchable dropdown untuk item (Select2 dengan AJAX)
- Status tracking: Belum Dibayar, Belum Dibayar Sepenuhnya, Sudah Dibayar
- Date range filter & status filter
- Edit/Delete hanya untuk status "Belum Dibayar"
- Soft delete untuk recovery data

### 💰 Payment Management (Split Payment)
- Multiple payments per sale
- Partial payment support
- Auto-generated payment code (PY-YYYYMMDD-XXXX)
- Automatic status update berdasarkan pembayaran
- Date & status filters
- View/Edit/Delete dengan permission

### 📦 Items Management
- CRUD items dengan image upload
- Auto-generated item code (ITM-YYYYMMDD-XXXX)
- Auto image resize ke maksimal 100KB (Intervention Image)
- Image preview sebelum upload
- Searchable item list (DataTables)
- Permission-based actions

### 👥 User Management
- CRUD users dengan role assignment
- 4 Role levels: Superadmin, Admin, Kasir, Staff
- 16 Granular permissions (sale/payment/user/item × CRUD)
- Role-based access control (Spatie Permission)

### 🔐 Authentication & Authorization
- Laravel Breeze authentication
- Email & password login
- Password reset
- Role & permission-based middleware
- Protected routes & views

## 🛠️ Tech Stack

- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0+
- **Frontend**: AdminLTE 3.2, Bootstrap 4.6, jQuery 3.6
- **Authentication**: Laravel Breeze 2.3.8
- **Authorization**: Spatie Laravel Permission
- **Image Processing**: Intervention Image Laravel 1.5.6
- **UI Components**: 
  - DataTables 1.13.4 (server-side processing)
  - Select2 4.1.0 (searchable dropdown dengan AJAX)
  - Chart.js 4.3.0 (data visualization)
  - Font Awesome 6.0 (icons)

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0 / MariaDB >= 10.3
- Node.js & NPM (untuk asset compilation)
- GD Library atau Imagick (untuk image processing)
- Apache/Nginx web server

## 🔧 Manual Installation

### 1. Clone Repository
```bash
git clone <repository-url> sales-app
cd sales-app
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration
Edit `.env` file dengan kredensial database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sales_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Create Database
```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE sales_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Seed database dengan roles, permissions, dan default users
php artisan db:seed
```

### 7. Storage Link
```bash
# Create symbolic link untuk public storage
php artisan storage:link
```

### 8. Set Permissions (Linux/Mac)
```bash
# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 9. Compile Assets (Optional)
```bash
# Development mode
npm run dev

# Production mode
npm run build
```

### 10. Run Application
```bash
# Development server
php artisan serve

# Akses di browser: http://localhost:8000
```

## 🔑 Default User Accounts

Setelah seeding, Anda dapat login dengan akun berikut:

| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| **Superadmin** | superadmin@example.com | password | Full access (16/16 permissions) |
| **Admin** | admin@example.com | password | 14 permissions (no user-delete, item-delete) |
| **Kasir** | kasir@example.com | password | Limited access |

## 📂 Project Structure

```
sales-app/
├── app/
│   ├── Helpers/
│   │   └── helpers.php              # Helper functions (generateCode, formatRupiah, etc.)
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Api/
│   │       │   ├── DataTableController.php    # DataTables API
│   │       │   └── ChartController.php        # Chart data API
│   │       ├── DashboardController.php
│   │       ├── ItemController.php
│   │       ├── PaymentController.php
│   │       ├── SaleController.php
│   │       └── UserController.php
│   └── Models/
│       ├── Item.php
│       ├── Payment.php
│       ├── Sale.php
│       ├── SaleItem.php
│       └── User.php
├── database/
│   ├── migrations/                   # Database schema
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       └── PermissionSeeder.php
├── public/
│   └── storage/                      # Symlink ke storage/app/public
├── resources/
│   └── views/
│       ├── dashboard/
│       ├── items/
│       ├── payments/
│       ├── sales/
│       ├── users/
│       └── layouts/
│           └── adminlte.blade.php    # Main layout
└── routes/
    └── web.php                       # Application routes
```

## 🔐 Permissions Matrix

| Permission | Superadmin | Admin | Kasir | Staff |
|------------|:----------:|:-----:|:-----:|:-----:|
| sale-create | ✅ | ✅ | ❌ | ❌ |
| sale-read | ✅ | ✅ | ❌ | ❌ |
| sale-update | ✅ | ✅ | ❌ | ❌ |
| sale-delete | ✅ | ✅ | ❌ | ❌ |
| payment-create | ✅ | ✅ | ❌ | ❌ |
| payment-read | ✅ | ✅ | ❌ | ❌ |
| payment-update | ✅ | ✅ | ❌ | ❌ |
| payment-delete | ✅ | ✅ | ❌ | ❌ |
| user-create | ✅ | ✅ | ❌ | ❌ |
| user-read | ✅ | ✅ | ❌ | ❌ |
| user-update | ✅ | ✅ | ❌ | ❌ |
| user-delete | ✅ | ❌ | ❌ | ❌ |
| item-create | ✅ | ✅ | ❌ | ❌ |
| item-read | ✅ | ✅ | ❌ | ❌ |
| item-update | ✅ | ✅ | ❌ | ❌ |
| item-delete | ✅ | ❌ | ❌ | ❌ |

## 🎯 Key Features Detail

### Auto Code Generation
- Sequential code generation dengan database lock
- Format: PREFIX-YYYYMMDD-XXXX
- Thread-safe dengan `FOR UPDATE` clause
- Retry mechanism untuk race condition handling

### Split Payment System
- Multiple partial payments per sale
- Automatic status calculation:
  - **Belum Dibayar**: No payment
  - **Belum Dibayar Sepenuhnya**: Partial payment
  - **Sudah Dibayar**: Fully paid
- Real-time remaining balance calculation

### Image Upload & Processing
- Auto resize image ke maksimal 100KB
- Iterative quality reduction (85% → 20%)
- Adaptive width reduction (800px → 400px)
- JPEG encoding untuk size optimization
- Preview sebelum upload

### Searchable Dropdown (Select2 + AJAX)
- Server-side search untuk handle large data
- 10 items per page dengan infinite scroll
- 250ms debounce untuk reduce API calls
- Result caching untuk better performance

## 🐛 Troubleshooting

### Error: SQLSTATE[23000] Duplicate entry
```bash
# Clear cache
php artisan optimize:clear

# Pastikan menggunakan transaction dengan lock
# Kode sudah menggunakan FOR UPDATE clause
```

### Storage link tidak bekerja
```bash
# Remove dan create ulang
rm public/storage
php artisan storage:link
```

### Permission denied error
```bash
# Set proper permissions (Linux/Mac)
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Select2 dropdown tidak muncul
```bash
# Clear browser cache
# Check console untuk JavaScript errors
# Pastikan jQuery dan Select2 loaded properly
```

## 📝 Development Notes

- Timezone: Asia/Jakarta
- Soft deletes enabled untuk semua main tables
- Database transactions untuk data consistency
- Server-side validation dengan custom messages
- Client-side validation untuk better UX

## 📄 License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).