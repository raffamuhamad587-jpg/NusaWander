# AGENTS.md

Repository context for NusaWander Laravel application.

## Tech Stack
- **Framework:** Laravel 11
- **Frontend:** Vue 3 + Inertia.js + Tailwind CSS
- **Bundler:** Vite
- **Database:** SQLite (tests: `:memory:`)
- **Roles:** `admin`, `wisatawan`

## Commands
- **Dev:** `php artisan serve` (with `npm run dev` for Vite). Use `composer dev` to run both simultaneously.
- **Tests:** `php artisan test` (uses SQLite in-memory as per `phpunit.xml`).

## Architecture & Conventions
- **Routing:** Public routes in `routes/web.php` use `App\Http\Controllers\Public` namespace. Admin routes are grouped under `/admin` with `admin` middleware.
- **Authentication:** Standard Laravel Breeze implementation. Custom `EnsureUserIsAdmin` middleware handles admin access.
- **Components:** Vue components located in `resources/js/Components`.
- **CSS:** Tailwind config contains brand colors under `theme.extend.colors.brand`. Use these for all UI components.

## Gotchas
- **Admin Middleware:** Always use `admin` alias in routes: `Route::middleware(['auth', 'admin'])`.
- **Testing:** `tests/Feature` tests use `RefreshDatabase` trait. Ensure factories match model expectations.
- **Deployment:** Standard Laravel asset build required (`npm run build`).
