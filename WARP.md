# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

**QVEX - Drive the Future** is a next-generation automotive marketplace and mobility platform connecting buyers, sellers, and renters through a secure, smart, and user-friendly ecosystem.

### Business Model
- **B2C:** Direct car sales and rentals to customers
- **B2B:** Vendor/dealership management with subscription plans
- **C2C:** Peer-to-peer car sales platform
- **Revenue:** Transaction fees, vendor subscriptions, featured listings, advertising

### Target Markets
- Primary: Arabic & English speaking regions
- Full RTL/LTR support with bilingual content
- Multi-currency support with localized pricing

### Key Differentiators
- **Dual Marketplace:** Both sales AND rental in one platform
- **Subscription-based Vendors:** Tiered subscription plans with listing limits
- **Comprehensive Vehicle Data:** VIN decoder, 360° views, full history
- **Advanced Search:** AI-powered with image search capabilities
- **Mobile-first Design:** Progressive Web App with native app APIs

## Development Commands

### Database Setup (MySQL with Laragon)
```bash
# Laragon provides MySQL out of the box
# Start Laragon and ensure MySQL service is running

# Create database using Laragon's HeidiSQL or command line:
# Right-click Laragon tray → MySQL → Open
# Or use Laragon's Terminal:

# Connect to MySQL (Laragon default: root with no password)
mysql -u root

# Create QVEX database
CREATE DATABASE qvex_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qvex_db;
EXIT;

# Alternative: Use Laragon's Quick Create Database
# Right-click Laragon → Quick app → Database → Create "qvex_db"
```

### Laragon Environment Configuration
```bash
# Laragon auto-creates virtual hosts
# Your app will be available at: http://qvex-backend.test
# Laragon automatically maps folder name to domain

# Enable Pretty URLs (if needed)
# Laragon → Menu → Apache → sites-enabled → Add qvex-backend.test

# SSL Support (optional)
# Right-click Laragon → SSL → qvex-backend.test
```

### Environment Setup
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies  
npm install

# Copy environment file
copy .env.example .env

# Configure database in .env file (Laragon MySQL defaults)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=qvex
# DB_USERNAME=root
# DB_PASSWORD=              (leave empty for Laragon default)
#
# IMPORTANT: Add this for Filament compatibility
# SESSION_DRIVER=database
# SESSION_CONNECTION=mysql  (fixes Filament session issues)

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed initial data (vehicle makes, models, etc.)
php artisan migrate:fresh --seed

# Option 1: Use Laravel's built-in server (with Laragon running in background)
composer run dev

# Option 2: Use Laragon's Apache server (recommended)
# Just start Laragon and visit http://qvex-backend.test
# Then run queue and Vite separately:
npm run dev                           # For frontend assets
php artisan queue:work               # For background jobs
php artisan pail --timeout=0        # For real-time logs
```

### QVEX-Specific Commands
```bash
# Vehicle data management
php artisan make:model VehicleMake -mfs
php artisan make:model VehicleModel -mfs
php artisan make:model VehicleTrim -mfs

# Generate Filament resources for core entities (with navigation groups)
php artisan make:filament-resource Vehicle --model=App\Models\Vehicle
php artisan make:filament-resource Vendor --model=App\Models\Vendor
php artisan make:filament-resource VendorSubscription --model=App\Models\VendorSubscription
php artisan make:filament-resource User --model=App\Models\User
php artisan make:filament-resource Transaction --model=App\Models\Transaction

# Create admin user for Filament dashboard
php artisan make:filament-user

# Filament maintenance commands
php artisan filament:upgrade              # Upgrade Filament assets
php artisan filament:install --panels     # Install panel functionality

# Create API resources for mobile/frontend
php artisan make:resource VehicleResource
php artisan make:resource VendorResource

# Generate form requests for validation
php artisan make:request VehicleStoreRequest
php artisan make:request VehicleUpdateRequest
php artisan make:request VendorRegistrationRequest
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run tests with filter
php artisan test --filter=testName

# Clear config before running tests (recommended)
php artisan config:clear && php artisan test
```

### Code Quality
```bash
# Format code with Laravel Pint
vendor/bin/pint --dirty

# Format all code
vendor/bin/pint
```

### Database Operations
```bash
# Create MySQL database (Laragon HeidiSQL or command line)
# Right-click Laragon → MySQL → Open HeidiSQL
# Or use Laragon Terminal: CREATE DATABASE qvex_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create new migration
php artisan make:migration create_table_name

# Create model with migration, factory, and seeder
php artisan make:model ModelName -mfs

# Check migration status
php artisan migrate:status

# Fresh migrate with seeders (includes vehicle makes/models)
php artisan migrate:fresh --seed

# Import vehicle data (when available)
php artisan vehicles:import-makes
php artisan vehicles:import-models

# Update search index
php artisan scout:import "App\Models\Vehicle"

# Clear application cache
php artisan optimize:clear

# Database maintenance
php artisan db:show                    # Show database info
php artisan schema:dump                # Dump current schema

# Check available artisan commands
php artisan list
```

### Asset Management
```bash
# Build assets for development (with Laragon)
npm run dev

# Build assets for production
npm run build
```

### Laragon Specific Tools & Workflow
```bash
# Access Laragon services via tray menu:
# - Right-click Laragon → MySQL → Open (HeidiSQL)
# - Right-click Laragon → Mail → MailHog (email testing)
# - Right-click Laragon → Terminal → Open Terminal

# Quick project setup in Laragon:
# 1. Place project in C:\laragon\www\qvex-backend
# 2. Auto-available at http://qvex-backend.test
# 3. SSL: Right-click Laragon → SSL → qvex-backend.test

# Useful Laragon shortcuts:
# Ctrl+Alt+T - Open Terminal in current directory
# Right-click folder → Laragon Terminal Here

# Mail testing with MailHog (built into Laragon):
# Configure in .env:
# MAIL_MAILER=smtp
# MAIL_HOST=127.0.0.1
# MAIL_PORT=1025
# Access MailHog at: http://localhost:8025

# Redis (if installed in Laragon):
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379
```

## Architecture Overview

### Core Domain Structure
- **Multi-vendor marketplace**: Vendors register, get verified, purchase subscriptions, and manage inventory
- **Dual marketplace**: Vehicles can be listed for SALE, RENT, or BOTH simultaneously
- **Vehicle hierarchy**: Makes → Models → Trims → Individual Vehicles (with full specs)
- **Subscription model**: Tiered vendor plans with listing limits, featured placement, analytics
- **Geographic distribution**: Location-based search with lat/lng coordinates
- **Multi-language**: Full Arabic/English support with RTL/LTR layouts

### Key Models & Relationships

#### User Management
- `User`: Base user model with multi-guard authentication (customers, vendors, admins)
- `Vendor`: Business entities (dealerships, rental companies, individuals, service centers)
- `VendorSubscription`: Manages subscription plans, listing limits, payment tracking
- `VendorStaff`: Staff members with role-based permissions within vendor accounts
- `UserAddress`: Multiple addresses per user for delivery/pickup
- `UserDocument`: KYC documents (ID, license, business registration)

#### Vehicle Structure (app/Models/Vehicle/) 
- `VehicleMake`: Car manufacturers (BMW, Toyota, etc.) with translations
- `VehicleModel`: Specific models within a make (Camry, X5, etc.) with year ranges
- `VehicleTrim`: Trim levels/variants with engine specs
- `Vehicle`: Individual listings with comprehensive specs, pricing (sale/rental rates)
- `VehicleFeature`: Standardized features (leather seats, sunroof, etc.)
- `VehicleDocument`: Service history, inspection reports, certificates
- `VehicleInquiry`: Customer inquiries with vendor responses
- `VehicleView`: Analytics tracking for page views

#### Transaction & Business Logic
- `Transaction`: Sales and rental transactions with status tracking
- `RentalAgreement`: Rental-specific terms, dates, security deposits
- `Payment`: Multiple payment gateways (Stripe, PayPal, local providers)
- `Review`: Vendor and vehicle reviews with verified purchase badges
- `Message`: In-app messaging between buyers/sellers with real-time chat
- `Notification`: Push, email, SMS notifications with preferences
- `Coupon`: Discount system with usage tracking
- `SavedSearch`: User search alerts with email notifications
- `UserFavorite`: Wishlist functionality

### Filament Admin Interface
- **Current Setup**: Filament 4.0.0 with custom QVEX branding
- **Panel URL**: `/dashboard` (configured as main admin panel)
- **Brand Configuration**: Custom QVEX colors applied (Emerald Green #2ECC71)
- **Brand Name**: "Qvex" (set in DashboardPanelProvider)
- **Admin User Created**: Access via `php artisan make:filament-user`
- **Navigation Structure**: 12 organized groups (Vehicle Management, Users & Vendors, etc.)
- **Packages Installed**: Media Library Plugin, Excel Export/Import
- **Session Fix Applied**: Fixed SESSION_CONNECTION to use MySQL

### Documentation Structure
```
docs/
├── README.md                           # Main documentation hub
├── requirements/
│   ├── qvex_full_requirements.md      # ✅ Complete 18K+ words specification
│   └── project_overview.md            # ✅ Technical summary and architecture
├── brand/
│   └── qvex_brand_colors.md          # ✅ Official QVEX brand colors & guidelines
├── filament/
│   ├── QVEX_Filament_Navigation_Guide.md  # ✅ Navigation structure (12 groups)
│   └── installed_packages.md          # ✅ Package tracking and usage examples
├── references/
│   └── qvex_development_reference.md  # ✅ Comprehensive development guide
├── database/                          # Future: Database schema documentation
├── architecture/                      # Future: System architecture docs
└── references/                        # External references and best practices
```

### Technology Stack
- **Backend**: Laravel 12.30.1 with PHP 8.3.16
- **Database**: MySQL 8.0+ (Development), PostgreSQL 15+ (Production recommended)
- **Cache/Queue**: Redis for caching and job queues
- **Admin Panel**: Filament 4.0.0 with custom QVEX branding
- **Search**: Laravel Scout with Algolia/Elasticsearch
- **Media Storage**: Spatie Media Library (Local/Cloud compatible)

### Model Architecture (Domain-Driven Design)
```
app/Models/
├── BaseModel.php               # Base functionality for all models
├── User.php                    # Core user model
├── Vehicle/                    # Vehicle domain
│   ├── Vehicle.php            # Main vehicle listings
│   ├── VehicleMake.php        # Car manufacturers
│   ├── VehicleModel.php       # Vehicle models
│   ├── VehicleTrim.php        # Trim levels/variants
│   ├── VehicleFeature.php     # Vehicle features
│   ├── VehicleDocument.php    # Vehicle documents
│   ├── VehicleInquiry.php     # Customer inquiries
│   └── VehicleView.php        # Analytics tracking
├── Vendor/                     # Vendor management
│   ├── Vendor.php             # Vendor businesses
│   └── VendorSubscription.php # Subscription plans
├── Customer/                   # User-related models
│   ├── UserAddress.php        # User addresses
│   └── UserDocument.php       # KYC documents
└── Communication/              # Reviews & messaging
    └── Review.php             # Reviews & ratings
```

#### Model Usage Examples
```php
// Vehicle domain models
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleMake;
use App\Models\Vehicle\VehicleModel;

// Customer domain models (User-related)
use App\Models\Customer\UserAddress;
use App\Models\Customer\UserDocument;

// Vendor domain models
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorSubscription;

// Communication models
use App\Models\Communication\Review;

// Main User model (stays at root level)
use App\Models\User;
use App\Models\BaseModel;
```

### Key Architectural Patterns

#### BaseModel Pattern
All domain models extend `BaseModel` which provides:
- **Media management**: Images ('images'), videos ('videos'), documents ('files')
- **Soft deletes and activation**: `is_active`, `deleted_at`
- **Filterable queries**: Advanced search capabilities
- **Translation support**: Arabic/English translations via Spatie Translatable
- **Common fields**: `added_by_id`, audit trails
- **Standardized methods**: `storeImages()`, `getImage()`, `deleteMedia()`

#### Migration Trait Pattern
Uses `MigrationTrait` for consistent database structure:
- Adds common fields (`is_active`, `added_by_id`, etc.)
- Standardizes indexing patterns
- Consistent foreign key relationships

#### Subscription Business Model
- **Tiered Plans**: Basic, Premium, Enterprise with different listing limits
- **Feature Control**: Number of listings, featured placements, analytics access
- **Payment Integration**: Stripe/PayPal with auto-renewal and dunning management
- **Usage Tracking**: Monitor listings used vs. plan limits
- **Commission Structure**: Platform takes percentage of transactions

## Development Guidelines

### QVEX Business Rules
- **Vehicle Status Flow**: draft → pending_approval → active → sold/rented
- **Vendor Verification**: Required before first vehicle listing
- **Dual Pricing**: Support both sale price AND rental rates (daily/weekly/monthly)
- **Geographic Requirements**: All vehicles must have city, state, country
- **Media Requirements**: Minimum 5 images per vehicle listing
- **Translation Requirements**: All customer-facing content needs Arabic translations
- **Commission Calculation**: Automatic deduction from vendor payments

### Laravel 12 Specific
- Uses streamlined Laravel 12 structure (no separate Kernel classes)
- Middleware registered in `bootstrap/app.php`
- Commands auto-register from `app/Console/Commands/`
- Use `casts()` method instead of `$casts` property in models

### Code Standards
- Follow existing naming conventions (descriptive method names)
- Use constructor property promotion in `__construct()`
- Always use explicit return type declarations
- Use Eloquent relationships over raw queries
- Create Form Request classes for validation
- Use queued jobs for time-consuming operations

### Testing Requirements
- Every change must have programmatic tests
- Use factories for test data creation
- Follow existing test conventions (`$this->faker` vs `fake()`)
- Run minimal tests with filters after changes

### Database Conventions
- Use proper Eloquent relationships with return types
- Prevent N+1 queries with eager loading
- Use `Model::query()` instead of `DB::`
- Include proper indexing for performance

### Media & Files
- Images stored in 'images' collection
- Videos in 'videos' collection  
- Documents in 'files' collection
- Use BaseModel methods for media operations

### Multi-language Support
- **Primary Languages**: English (LTR) and Arabic (RTL)
- **Database Fields**: `name` and `name_ar`, `description` and `description_ar`
- **Spatie Translatable**: For complex content with `$translatable = ['field']`
- **Frontend Support**: Full RTL layout support required
- **User Preferences**: Language and timezone settings per user
- **Content Requirements**: All customer-facing content needs both languages

## Important QVEX-Specific Notes

### Business Logic Requirements
- **Vendor Verification**: All vendors must be verified before listing vehicles
- **Subscription Enforcement**: Check vendor subscription limits before allowing new listings
- **Dual Market Support**: Always consider both SALE and RENTAL pricing in forms/APIs
- **Geographic Requirements**: City, state, country required for all vehicles
- **VIN Validation**: Unique VIN numbers required for used vehicles
- **Commission Calculation**: Automatic platform commission deduction from vendor payments
- **Media Standards**: Min 5 images, support 360° views and videos

### Technical Requirements
- **Never use `env()` outside config files** - always use `config('key')`
- **Run Pint before committing** - `vendor/bin/pint --dirty`  
- **Use named routes** - prefer `route('name')` over hardcoded URLs
- **Queue heavy operations** - image processing, email sending, notifications
- **Test coverage required** - especially for transaction flows
- **API versioning** - all APIs under `/api/v1/` namespace
- **Rate limiting** - 100 requests/minute authenticated, 30 for guests

## Common Issues

### Frontend Build Errors
If frontend changes don't reflect, run:
```bash
npm run build
# or
npm run dev
```

### Vite Manifest Errors
```bash
npm run build
```

### Database Issues
Check migration order and foreign key constraints. Vehicle hierarchy must be created in order: Makes → Models → Trims → Vehicles.

### Search Issues
If vehicle search is not working:
```bash
php artisan scout:import "App\Models\Vehicle"
php artisan scout:flush "App\Models\Vehicle"
php artisan scout:import "App\Models\Vehicle"
```

### Media Upload Issues
Ensure storage is properly configured:
```bash
php artisan storage:link
```

### Database Connection Issues
If having MySQL connection issues with Laragon:
```bash
# Test database connection
php artisan db:show

# Check if Laragon services are running
# Right-click Laragon tray icon → Check service status

# Restart Laragon services if needed
# Right-click Laragon → Stop All → Start All

# Check Laragon MySQL port (usually 3306)
# Laragon → Menu → MySQL → Change Port (if needed)

# Verify .env database configuration
php artisan config:show database.connections.mysql

# Common Laragon database settings:
# DB_HOST=127.0.0.1 or localhost
# DB_USERNAME=root
# DB_PASSWORD= (empty by default)
```

### Translation Issues
Clear translation cache:
```bash
php artisan translatable:clear-cache
```

## API Endpoints Structure

### Authentication
```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/verify-otp
```

### Vehicles
```
GET    /api/v1/vehicles              # Search with filters
GET    /api/v1/vehicles/{id}         # Vehicle details
POST   /api/v1/vehicles              # Create listing (vendors)
PUT    /api/v1/vehicles/{id}         # Update listing
GET    /api/v1/vehicles/search       # Advanced search
GET    /api/v1/vehicles/featured     # Featured listings
```

### Vendors
```
GET    /api/v1/vendors               # Vendor directory
GET    /api/v1/vendors/{id}          # Vendor profile
POST   /api/v1/vendors/register      # Vendor registration
GET    /api/v1/vendors/{id}/vehicles # Vendor inventory
```

### Transactions
```
POST   /api/v1/transactions/sale     # Purchase request
POST   /api/v1/transactions/rental   # Rental booking
GET    /api/v1/transactions/{id}     # Transaction status
```

## Brand Guidelines

### Color Palette
- **Primary**: Emerald Green (#2ECC71)
- **Secondary**: Forest Green (#27AE60) 
- **Accent**: Warm Cream (#FFE7BB)
- **Text**: Dark Navy (#2C3E50)
- **Background**: Pure White (#FFFFFF)

### CSS Variables
```css
:root {
  --primary: #2ECC71;
  --primary-hover: #27AE60;
  --secondary: #FFE7BB;
  --text-primary: #2C3E50;
  --background: #FFFFFF;
}
```
