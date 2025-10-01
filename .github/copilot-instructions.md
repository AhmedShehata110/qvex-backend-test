<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.16
- filament/filament (FILAMENT) - v4
- laravel/framework (LARAVEL) - v12
# Copilot instructions — qvex-backend (concise)

This is a Laravel 12 backend (PHP 8.3), Filament v4 admin UI and Livewire v3. Keep guidance short and repository-specific.

- Big picture: backend-only app. Admin UI is Filament resources in `app/Filament/Resources`. Business logic lives in `app/Models`, `app/Http/Controllers`, `app/Traits`, and observers in `app/Observers`. Bootstrap & route wiring live in `bootstrap/app.php` and `bootstrap/providers.php`.

- Key integrations: Filament + Livewire + Vite/Tailwind (frontend assets in `resources/`), Spatie MediaLibrary, Spatie Permission, and Sanctum. Default DB file: `database/database.sqlite` (check `.env`).

- Important commands (use these exact scripts):
  - Start dev environment: `composer run dev` (starts server, queue listener, pail, and `npm run dev` via concurrently)
  - Run tests: `composer run test` or `php artisan test` (use `--filter` to limit runs)
  - Format code: `vendor/bin/pint --dirty` before finalizing changes
  - Build assets: `npm run build` if Vite manifest issues appear

- Patterns & locations to check before editing:
  - Filament resources: `app/Filament/Resources/*` (Schemas/, Tables/, Actions/)
  - Models & traits: `app/Models`, `app/Traits`, `app/BaseModel.php`
  - Observers: `app/Observers/*` (registered via providers/bootstrap)
  - Helpers: `app/Helpers/helpers.php` (composer autoload files)

- Testing & scaffolding rules:
  - Use PHPUnit (phpunit v11). Tests live in `tests/Feature` and `tests/Unit` and should use factories in `database/factories`.
  - Use Filament and Livewire test helpers (`Livewire::test()` / `livewire()`), and authenticate when testing Filament UIs.
  - Prefer `php artisan make:*` generators and pass `--no-interaction` for reproducible scaffolding.

- Safety rules (must follow):
  - Do NOT add/upgrade Composer dependencies or change root composer.json without approval.
  - Keep files under existing directories; follow sibling file structure and naming.
  - Run the minimal set of related tests and include test output in your change summary.

- Files to read first: `bootstrap/app.php`, `bootstrap/providers.php`, `composer.json`, `package.json`, `routes/web.php`, `app/Filament/Resources`, `app/Models`, `database/migrations`.

If you want small examples (Filament resource stub, Livewire test snippet, or example migration), say which one and I will add it.

## MCP findings (auto-generated)

- MCP snapshot (ran via Laravel Boost tools): the app exposes ~159 routes — many are Filament dashboard resources under `dashboard/*` (administration, content, ecommerce, locations, marketing, vehicle-management, sales-and-transactions, users-and-vendors, etc.). Examples: `dashboard/vehicle-management/vehicles`, `dashboard/content/blog-posts`, `dashboard/sales-and-transactions/orders` and Filament endpoints like `filament/exports/{export}/download` and `livewire/*` endpoints.

- Artisan commands discovered: full Laravel set plus many Filament and Livewire generators. Notable commands available in this repo:
  - Filament generators: `filament:make-resource`, `filament:make-panel`, `filament:make-user`, `filament:install`, `filament:optimize` etc.
  - Livewire tooling: `livewire:make`, `livewire:form`, `livewire:upgrade` (v2→v3 helper), and frontend asset endpoints (`livewire/livewire.js`).
  - Common helpers: `make:model`, `make:observer`, `make:migration`, `make:factory`, `make:test`, `migrate`, `migrate:fresh`, `queue:listen`, `pail` (tails logs), and MCP commands (`mcp:start`, `mcp:inspector`).

- DB schema note: the MCP schema tool failed with "SQLSTATE[HY000] [1049] Unknown database 'qvex'" when attempting to read the MySQL schema. The repository also contains `database/database.sqlite` but the runtime configuration appears to use MySQL (check `.env` and `config/database.php`). Before any code that depends on the DB schema, ensure the DB connection in `.env` is correct or ask the user for DB credentials / to run migrations.

- Recommended MCP workflows for agents working on this repo:
  1. Use `list-routes` (MCP) or `php artisan route:list` to confirm UI surface and resource slugs before editing Filament resources.
  2. Use `list-artisan-commands` (MCP) to find the correct Filament/Livewire generator to scaffold new resources; always pass `--no-interaction`.
  3. Use `tinker` (MCP) or `database-query` for read-only data inspection; avoid destructive commands (`migrate:fresh`, `db:wipe`) unless asked.
  4. If schema is needed, fix DB connection (or provide credentials) then re-run the MCP `database-schema` tool. Share the exact command and schema output in change summaries.

- Quick actionable examples for AI agents:
  - To find where a Filament resource is routed: inspect `Route::get` entries in `bootstrap/app.php` and search `app/Filament/Resources/*` for the resource class matching the route slug (e.g., `dashboard/vehicle-management/vehicles` → check `app/Filament/Resources/Vehicle/` or similar).
  - To scaffold a resource: prefer `php artisan filament:make-resource` or `php artisan make:filament-resource` variants (confirm with `list-artisan-commands`).

If you'd like, I can (with your confirmation):
- fetch the full `route:list` output as a saved text file, or
- re-run the DB schema after you confirm how to connect (use `.env` or provide credentials), or
- add a small Filament resource example and a Livewire test snippet to the instructions.

## Brand & Requirements (from repo docs)

- Brand colors: primary greens (Emerald `#2ECC71`, Forest `#27AE60`, Lime `#A4D65E`, Spring `#58D68D`) with supporting Warm Cream `#FFE7BB`, Dark Navy `#2C3E50`, Steel Gray `#7F8C8D` and Light Gray `#ECF0F1`. CSS variables are defined in `qvex_brand_colors.md` — prefer those variables (`--primary`, `--primary-hover`, `--accent`, `--premium`) in CSS/Blade views.
- Accessibility: color combinations meet WCAG 2.1 AA per the brand doc; maintain contrast, provide non-color indicators, and test colorblind scenarios.
- Design usage: primary green for CTAs and brand elements; warm cream for premium highlights (featured listings, VIP badges); navy/steel for text and professional elements.
- Product rules (from `qvex_full_requirements.md`): backend is Filament admin for Super Admin only; public-facing features served via API (Sanctum) to vendor/customer dashboards and mobile apps.
- Must-haves to respect in code changes: RTL/LTR support, bilingual content (Arabic + English), multi-role permissions (Spatie), KYC/document uploads, multi-image uploads for vehicles, and compliance requirements (OWASP, PCI, GDPR where applicable).
- Non-functional targets to keep in mind: page load <2s, API latency <500ms for critical endpoints, support for 10k concurrent users, caching (Redis), CDN for assets, and queueing for heavy tasks.

For details, refer to `qvex_brand_colors.md` and `qvex_full_requirements.md` in the repo root.

## External docs & examples

- Filament 4 docs (version-specific): https://filamentphp.com/docs/4.x — use this first for Filament components, resource patterns, and generator flags. Example: confirm `filament:make-resource` options here before scaffold.
- Laravel 12 docs: https://laravel.com/docs/12.x — use for framework features, queues, caching, and config conventions.
- Filament official demo repo: https://github.com/filamentphp/demo — reference implementation for panels, resources, and layouts; search this repo for examples of complex resource setups.

Use these external resources to verify idiomatic patterns before making changes; cite specific doc links in PR descriptions when applicable.
</laravel-boost-guidelines>
