# QVEX Backend - AI Coding Agent Instructions

## Foundation Rules

**Laravel Boost Guidelines** - These guidelines are specifically curated by Laravel maintainers for this application and should be followed closely.

- **PHP Version:** 8.3.16
- **Laravel Version:** v12
- **Filament Version:** v4
- **Livewire Version:** v3

## Big Picture Architecture

QVEX is a **backend-only Laravel application** serving a multi-vendor car marketplace platform. The architecture separates administrative functions from user-facing features:

- **Admin Panel:** Filament v4 interface for Super Admin only (`/dashboard/*` routes)
- **API Layer:** Laravel Sanctum-powered RESTful APIs serving external vendor/customer dashboards and mobile apps
- **Data Flow:** Admin panel manages content → APIs serve data to frontend applications
- **Database:** SQLite for development (default), MySQL for production

**Key Architectural Decisions:**
- Domain-driven organization: Resources grouped by business domains (VehicleManagement, SalesAndTransactions, etc.)
- BaseModel pattern: All models extend `BaseModel` with common traits and fillable fields
- Audit logging: Comprehensive audit trails via observers with custom tagging
- Multi-tenancy: Vendor isolation with staff management and permissions

## Critical Developer Workflows

### Development Environment
```bash
# Start complete dev environment (server + queue + logs + vite)
composer run dev

# Individual services
php artisan serve              # Laravel server
php artisan queue:listen       # Queue worker
php artisan pail               # Log tailing
npm run dev                    # Vite frontend assets
```

### Testing & Quality
```bash
# Run all tests
composer run test
# or
php artisan test --filter=TestName

# Format code (Laravel Pint)
vendor/bin/pint --dirty

# Build production assets
npm run build
```

### Database Operations
```bash
# Fresh database with seeders
php artisan migrate:fresh --seed

# Check database status
php artisan migrate:status
```

## Project-Specific Patterns & Conventions

### Filament Resource Structure
**Pattern:** Each resource follows a consistent structure in `app/Filament/Resources/{Domain}/{Resource}/`:

```
VehicleResource.php          # Main resource class
├── Pages/                   # CRUD pages
│   ├── ListVehicles.php
│   ├── CreateVehicle.php
│   ├── EditVehicle.php
│   └── ViewVehicle.php
├── Schemas/                 # Form and infolist schemas
│   ├── VehicleForm.php
│   └── VehicleInfolist.php
└── Tables/                  # Table configuration
    └── VehiclesTable.php
```

**Example Resource Structure:**
```php
class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;
    protected static string $navigationGroup = 'Vehicle Management';

    public static function form(Schema $schema): Schema
    {
        return VehicleForm::configure($schema);  // Separate schema class
    }

    public static function table(Table $table): Table
    {
        return VehiclesTable::configure($table); // Separate table class
    }
}
```

### Model Architecture
**All models extend BaseModel** (`app/Models/BaseModel.php`) which provides:

- **Common Traits:** `HasActivation`, `HasTranslations`, `InteractsWithMedia`, `SoftDeletes`
- **Base Fillable:** `['is_active', 'added_by_id']` (auto-merged with child fillable)
- **Common Methods:** `isActive()`, `active()`, `inActive()`, `toggleActivation()`

**Example Model:**
```php
class Vehicle extends BaseModel
{
    use Filterable; // Additional trait for this model

    protected $fillable = [
        'title', 'price', 'description',
        // 'is_active', 'added_by_id' automatically included from BaseModel
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
    ];
}
```

### Audit Logging Pattern
**Comprehensive audit trails** via observers (`app/Observers/`) with custom tagging:

```php
class UserObserver
{
    use BaseAuditObserver;

    protected function getCustomAuditTags($model, string $event): array
    {
        return [
            'user_type_' . $model->user_type->value,
            $model->is_active ? 'active_user' : 'inactive_user',
        ];
    }
}
```

### Helper Functions
**Global helpers** in `app/Helpers/helpers.php` (auto-loaded via composer):

```php
// Convert various inputs to arrays
convertToArray($value) // string, array, Collection → array

// Generate UUIDs
uuid() // Returns string UUID
```

## Integration Points & Dependencies

### Core Integrations
- **Filament v4 + Livewire v3:** Admin panel with reactive components
- **Spatie MediaLibrary:** File uploads and media management
- **Spatie Permission:** Role-based access control (RBAC)
- **Spatie Translatable:** Multi-language content (Arabic/English)
- **Laravel Sanctum:** API authentication for external apps

### Frontend Assets
- **Vite + Tailwind CSS:** Modern build system
- **Custom Brand Colors:** Defined in `Filament\DashboardPanelProvider`
- **RTL/LTR Support:** Built-in for Arabic/English locales

### External Service Integration Points
- **Queue System:** For heavy tasks (email, notifications, processing)
- **Cache/Redis:** For performance optimization
- **CDN:** For asset delivery (planned)
- **Payment Gateways:** Integration points for transactions

## Business Logic Patterns

### Activation Pattern
**All entities use activation status** instead of soft deletes for business logic:

```php
// Check status
$vehicle->isActive()      // boolean
$vehicle->isInActive()    // boolean

// Toggle status
$vehicle->active()        // activate
$vehicle->inActive()      // deactivate
$vehicle->toggleActivation() // toggle
```

### Translation Pattern
**Multi-language support** for Arabic/English content:

```php
// In migrations
$table->json('name');     // Stores translations as JSON
$table->json('description');

// In models
use HasTranslations;

protected $translatable = ['name', 'description'];

// Usage
$vehicle->setTranslation('name', 'ar', 'الاسم العربي');
$vehicle->getTranslation('name', 'ar');
```

### Media Upload Pattern
**Spatie MediaLibrary integration** for file management:

```php
// In models
use InteractsWithMedia;

public function registerMediaCollections(): void
{
    $this->addMediaCollection('images')->acceptsMimeTypes(['image/*']);
    $this->addMediaCollection('documents')->acceptsMimeTypes(['application/pdf']);
}
```

## Files to Read First

**Essential understanding files:**
1. `bootstrap/app.php` - Application structure and routing
2. `bootstrap/providers.php` - Service providers registration
3. `app/Providers/Filament/DashboardPanelProvider.php` - Admin panel configuration
4. `app/Models/BaseModel.php` - Base model with common functionality
5. `composer.json` - Dependencies and scripts
6. `qvex_brand_colors.md` - Brand color specifications
7. `qvex_full_requirements.md` - Business requirements and architecture

## Testing Patterns

### Test Structure
- **PHPUnit v11** with Laravel test helpers
- **Feature Tests:** `tests/Feature/` for integration tests
- **Unit Tests:** `tests/Unit/` for isolated testing
- **Filament Testing:** Use `Livewire::test()` for admin panel tests

### Test Example
```php
use Livewire\Livewire;

class VehicleResourceTest extends TestCase
{
    public function test_can_create_vehicle()
    {
        Livewire::test(CreateVehicle::class)
            ->fillForm([
                'title' => 'Test Vehicle',
                'price' => 10000,
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }
}
```

## Safety Rules

- **DO NOT** add/upgrade Composer dependencies without approval
- **Keep files** under existing directory structure
- **Run tests** after changes: `composer run test`
- **Format code** before committing: `vendor/bin/pint --dirty`
- **Database:** Use SQLite for dev, verify MySQL config for production

## Common Gotchas

- **Database Connection:** MCP schema tool may fail - check `.env` for correct DB credentials
- **Media Library:** Files stored via Spatie MediaLibrary, not direct filesystem
- **Translations:** Use `HasTranslations` trait, not Laravel's built-in translation
- **Permissions:** Use Spatie Permission package for RBAC
- **Routes:** All admin routes under `/dashboard/*`, API routes separate

## External Resources

- **Filament v4 Docs:** https://filamentphp.com/docs/4.x
- **Laravel 12 Docs:** https://laravel.com/docs/12.x
- **Spatie Packages:** Check individual package docs for advanced usage

---

*Last updated: October 2025 - Based on Laravel 12, Filament v4, Livewire v3 ecosystem*
