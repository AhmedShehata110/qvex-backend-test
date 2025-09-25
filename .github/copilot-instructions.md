<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.16
- filament/filament (FILAMENT) - v4
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- livewire/livewire (LIVEWIRE) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11


## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.
## Quick guide for AI agents (QVEX backend)

Purpose: concise, actionable rules so an AI coding agent can make correct, low-risk edits in this Laravel 12 + Filament 4 app.

- Environment & key packages: PHP 8.3+, Laravel 12, Filament v4, Livewire v3, Spatie medialibrary v11, spatie/permission. See `composer.json` for versions.

- Critical commands
  - Dev: `composer run dev` (composer script runs concurrently: `php artisan serve`, queue listener, pail, and `npm run dev`). See `composer.json` -> `scripts.dev`.
  - Frontend build: `npm run dev` (dev) and `npm run build` (production, Vite). See `package.json`.
  - Tests: `php artisan test` (or `php artisan test --filter=TestName`).
  - Formatter: `vendor/bin/pint --dirty` before merging changes.

- Project layout & routing notes
  - Routes and console commands are wired in `bootstrap/app.php` (not `app/Console/Kernel.php`).
  - Service providers live in `bootstrap/providers.php` (app-level providers are registered there).
  - Filament UI resources live under `app/Filament/Resources` and follow Filament v4 structure (Schemas, Tables, Actions directories).

- Conventions and patterns to follow (concrete)
  - Models inherit `App\Models\BaseModel` which merges `baseFillable` with child `$fillable` and provides media helpers (`storeImages`, `getImage`, etc.). Example: `app/Models/BaseModel.php`.
  - Use Eloquent relationships and eager loading to avoid N+1 (`Model::query()` preferred over raw `DB::` queries).
  - Filament components use static `make()` initialization and Filament-specific artisan generators (use `php artisan` help or `list-artisan-commands` if available).
  - Files in `app/Helpers/helpers.php` are auto-loaded via composer `files` autoload.

- Tests & Filament specifics
  - Filament pages/components require authentication in tests. Use `Livewire::test()` and `assertSeeLivewire()` where appropriate.
  - When adding tests, prefer feature tests and factories (`database/factories/`). Run only the affected test during development.

- Media, files & uploads
  - The project uses Spatie Media Library; models call `$this->addMedia(...)->toMediaCollection(...)`. Preserve collection names (commonly `images`, `videos`, `files`). See `BaseModel` for examples.

- Safe edit checklist for PRs
  1. Run `vendor/bin/pint --dirty` and fix formatting issues.
  2. Run targeted tests: `php artisan test --filter=...` until green.
  3. If UI changes: run `npm run dev` and `composer run dev` as needed; if assets missing for production, run `npm run build`.

- What not to change or assume
  - Don't create new top-level directories without approval. Follow existing structure (Models, Filament/Resources, Http/Controllers).
  - Don't call `env()` outside config files; read config via `config('...')`.

- Docs & specs (authoritative)
  - The repository `docs/` folder is the single source of project requirements, implementation notes, and Filament admin guidance. Always consult it before proposing design changes.
  - Key files to check first:
    - `docs/README.md` — documentation index & templates (use when adding docs or templates).
    - `docs/PROJECT_OVERVIEW.md` — high-level architecture, important files, and developer notes (includes sqlite/testing config and BaseModel caveats).
    - `docs/FILAMENT_4_USAGE.md` — Filament install/scaffold guidance and project-specific notes (do NOT run `--scaffold` on an existing project without approval).
    - `docs/filament/QVEX_Filament_Navigation_Guide.md` — navigation & Filament panel specifics (use when adding resources or widgets).
    - `docs/requirements/qvex_full_requirements.md` — product requirements and acceptance criteria (reference for feature work).
  - Usage guidance:
    - Reference the appropriate docs file in your PR description and link specific sections (e.g., "implements requirement X from docs/requirements/qvex_full_requirements.md").
    - If a doc contradicts code, prefer the code but flag the discrepancy in an issue and ask for clarification.
    - Use the doc templates in `docs/README.md` when adding or updating documentation.

If anything here is unclear or you want additional examples (common Filament resource patterns, example tests, or a PR checklist), tell me which area to expand.

---

## Boost + MySQL (mandatory for agents)

- Before any code changes or database work, call the Boost application-info tool to confirm runtime details (PHP/Laravel versions and the configured DB engine). Use this data to choose the correct DB tools and patterns.
- Use these MCP Boost tools whenever you need authoritative runtime or codebase metadata:
  - `application-info` — confirm PHP/Laravel versions and DB engine.
  - `list-artisan-commands` — check available artisan generators and options (always pass `--no-interaction`).
  - `list-routes` — confirm existing endpoints and Filament resource routes.
  - `database-schema` / `database-query` — read-only DB schema and SELECT queries.
  - `tinker` — execute safe PHP snippets for debugging (prefer read-only operations unless user requests changes).
  - `get-absolute-url` — produce absolute app URLs for verification.

- Database rule: if `application-info` reports MySQL as the configured engine, prefer the MySQL DB client extension (`dbclient-execute-query` / equivalent) for read-only, parameterized SELECTs and schema introspection. Use `mcp_laravel-boost_database-query` for generic read-only queries when appropriate. Never run data-modifying SQL without explicit user approval.

- Safety guidelines when using DB tools:
  - Default to read-only SELECT queries. If you must run DDL/DML, ask the user and show the exact SQL that will run.
  - Avoid running `migrate:fresh`, `migrate:refresh`, or any destructive commands unless asked and the environment is explicitly development.
  - When returning query results, omit any secrets or sensitive fields (API keys, secrets, tokens) unless the user explicitly requests them and has the rights to access them.

- Examples (illustrative):
  - Confirm environment then inspect vehicles table:
    1. Call `application-info`.
    2. If DB=MySQL, call `dbclient-execute-query` with a parameterized SELECT (read-only) to sample rows: `SELECT id, title, price FROM vehicles LIMIT 5`.
  - To find a route: call `list-routes` and search for `dashboard/vehicle-management/vehicles`.

- Use Boost first, code second: always consult Boost tools for environment and routing before making edits or running generators. Record Boost outputs (versions/DB engine/route paths) in PR descriptions when relevant.