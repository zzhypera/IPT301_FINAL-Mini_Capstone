# Final Project Requirement Checklist

- [x] Laravel MVC structure with named routes and resource controllers
- [x] Exactly five application/domain migrations: customers, devices, technicians, repair_jobs, payments
- [x] Foreign-key relationships and Eloquent relationships across all five domain tables
- [x] CRUD screens for customers, devices, technicians, repair jobs, and payments
- [x] Blade views with Bootstrap 5 CDN and responsive layouts
- [x] Server-side validation and readable success/error feedback
- [x] Delete confirmation prompts
- [x] Factories for all five domain models
- [x] DatabaseSeeder with related sample records
- [x] Dashboard with summary metrics and recent repair jobs
- [x] Payment balance calculations and prevention of overpayment
- [x] Feature tests for dashboard, customer creation, repair-job relationships, and overpayment validation
- [x] README setup instructions and safe `.env.example`

## Verification note
PHP syntax checks were run for all PHP files. Full Laravel feature tests and live database migrations still need to be run on a machine with Composer dependencies installed (`composer install`), because the supplied project archive did not include `vendor/` and Composer is not available in the current build environment.
