QVEX - Backend

Quickstart

1. Copy env

   cp .env.example .env

2. Install PHP deps

   composer install

3. Install JS deps

   npm ci

4. Start dev environment

   composer run dev

Useful commands

- Run migrations & seeders: php artisan migrate --seed
- Run tests: composer run test
- Format: vendor/bin/pint --dirty

Project overview

- Laravel 12 backend with Filament v4 admin UI (app/Filament/Resources)
- Livewire v3 used for interactive components
- Key folders: app/Models, app/Http/Controllers, app/Observers, app/Traits

Docs & links

- Filament docs: https://filamentphp.com/docs/4.x
- Laravel 12 docs: https://laravel.com/docs/12.x
