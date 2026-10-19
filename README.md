# Computer Repair Shop (Laravel)

A database-driven repair shop management system built with Laravel, Blade, and Bootstrap 5.

## Requirements
- PHP 8.3+
- Composer
- MySQL/MariaDB (or a configured supported Laravel database)

## Setup
1. Install dependencies: `composer install`
2. Copy environment file: `Copy-Item .env.example .env` (PowerShell)
3. Generate key: `php artisan key:generate`
4. Configure database credentials in `.env` and create the database.
5. Run migrations and sample data: `php artisan migrate --seed`
6. Start the server: `php artisan serve`
7. Visit `http://127.0.0.1:8000`.

For a disposable development database only, `php artisan migrate:fresh --seed` drops all existing tables before rebuilding them.

## View structure
Each business module uses one main Blade file, as requested:
- `resources/views/customer.blade.php`
- `resources/views/device.blade.php`
- `resources/views/technician.blade.php`
- `resources/views/repair-job.blade.php`
- `resources/views/payment.blade.php`

Each module view switches between list, form, and (for repair jobs) detail modes using the `$mode` variable. The dashboard and shared application layout remain separate because they are shared application pages, not module-specific CRUD views.

## Tests
Run `php artisan test` after installing dependencies and configuring a test database.
