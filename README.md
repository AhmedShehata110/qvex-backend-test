## QVEX Backend

QVEX is the backend for the QVEX multi-vendor car marketplace. This repository contains the Laravel 12.x based API, configuration, and application code powering listings, vendors, transactions, and administration tools.

Key points:

- PHP: ^8.2
- Framework: Laravel 12.x
- Testing: PHPUnit 11.x

Quick start

1. Copy `.env.example` to `.env` and update credentials.
2. Install PHP dependencies: `composer install`
3. Generate app key: `php artisan key:generate`
4. Run migrations: `php artisan migrate --seed`
5. Run tests: `php artisan test`

For local frontend assets (if present): run `npm install` and `npm run dev` or `npm run build`.

Contributing

See `CONTRIBUTING.md` (if present) or open issues for bug reports and feature requests.

License

This project is licensed under the MIT license.
You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.
