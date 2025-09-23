Filament 4.x Integration Guide (for Laravel 12)

This document summarizes Filament 4.x installation and integration steps tailored for this project (Laravel 12, Windows PowerShell). References in this guide assume the project brand name is "Qvex".

Prerequisites
- PHP 8.2+ (project uses PHP 8.3)
- Composer
- Node.js + npm (for frontend build)
- Livewire 3.x (Filament requires Livewire)
- Tailwind CSS

Quick install (recommended for existing projects)

PowerShell-friendly composer install (PowerShell treats ^ specially):

```powershell
composer require \
    filament/tables:"~4.0" \
    filament/schemas:"~4.0" \
    filament/forms:"~4.0" \
    filament/infolists:"~4.0" \
    filament/actions:"~4.0" \
    filament/notifications:"~4.0" \
    filament/widgets:"~4.0"
```

Install Filament frontend scaffolding (existing project):

```powershell
php artisan filament:install
npm install
npm run dev
```

Notes
- If you are installing into a new project and want Filament to scaffold layouts and auth, run `php artisan filament:install --scaffold`. This will overwrite certain frontend/layout files.
- Filament uses Livewire components and Tailwind — ensure `resources/css/app.css` includes Tailwind directives and is compiled by Vite.
- For CI / private packages: follow composer http-basic auth instructions (e.g., for Flux/Pro packages). See project CI secrets for composer credentials.

Auth & Admin User
- Filament uses your Laravel auth configuration. If you use a custom user model or multiple guards, set `Filament::auth()` or adjust middleware accordingly in `config/filament.php` or in a service provider.
- Create an admin user manually via tinker or a seeder:

```php
// tinker example
\App\Models\User::factory()->create(['email' => 'admin@example.com', 'is_admin' => true]);
```

Resource scaffolding (example: Vehicle)

1. Generate a Filament resource:

```powershell
php artisan make:filament-resource Vehicle --model=\App\Models\Vehicle
```

2. Edit the generated Resource's `form()` and `table()` methods to match your fields. Use relation managers for relationships (images, owners, etc.).

Render Hooks & Customization Points
- Filament exposes render hooks for injecting content into panel layout (e.g., `panel::BODY_START`, `panel::SIDEBAR`, `page::HEADER`). Use view composers or Filament's `RenderHook` features to hook in widgets or custom panels.
- Forms: Use `->schema([...])` and the rich field types (`TextInput`, `Select`, `Repeater`, `FileUpload`). For relation fields, use `BelongsToSelect`, `HasManyRepeater`, or relation managers.
- Tables: Use `Tables	able()` with columns, filters, and actions. Use `Actionsormsorm()` for modal forms. Add custom bulk actions with `TablesulkActions()`.

Recommended Plugins / Next Steps
- filament/spatie-laravel-permission integration if you use Spatie permissions.
- filament/shield for admin auth scaffolding.
- filament/notifications for in-admin notifications.

Project-specific notes
- Do not run `--scaffold` on an existing project unless you intend to overwrite layout files. Use manual integration flow instead.
- Ensure packages referenced in `BaseModel` (Spatie MediaLibrary, Spatie Translatable, enums) are installed before using Filament resources that rely on them.

References
- Filament docs: https://filamentphp.com/docs/4.x
