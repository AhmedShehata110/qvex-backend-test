<?php

use App\Traits\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use MigrationTrait;

    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('make_id')->constrained('vehicle_makes');
            $table->foreignId('model_id')->constrained('vehicle_models');
            $table->foreignId('trim_id')->nullable()->constrained('vehicle_trims');
            $table->string('vin', 17)->unique()->nullable();
            $table->integer('year');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('condition', ['new', 'used', 'certified_preowned', 'salvage']);
            $table->enum('availability_type', ['sale', 'rent', 'both']);
            $table->enum('status', ['draft', 'active', 'sold', 'rented', 'inactive', 'pending_approval']);
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('original_price', 12, 2)->nullable();
            $table->boolean('is_negotiable')->default(true);
            $table->decimal('rental_daily_rate', 8, 2)->nullable();
            $table->decimal('rental_weekly_rate', 8, 2)->nullable();
            $table->decimal('rental_monthly_rate', 8, 2)->nullable();
            $table->decimal('security_deposit', 8, 2)->nullable();
            $table->integer('mileage')->nullable();
            $table->enum('mileage_unit', ['km', 'miles'])->default('km');
            $table->string('exterior_color');
            $table->string('interior_color')->nullable();
            $table->integer('doors')->nullable();
            $table->integer('cylinders')->nullable();
            $table->string('license_plate')->nullable();
            $table->json('additional_specs')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country', 2);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('has_warranty')->default(false);
            $table->text('warranty_details')->nullable();
            $table->date('warranty_expires_at')->nullable();
            $table->date('last_service_date')->nullable();
            $table->integer('service_interval_km')->nullable();
            $table->text('service_history')->nullable();
            $table->string('slug')->unique();
            $table->json('seo_keywords')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // General fields
            $this->addGeneralFields($table);

            $table->index(['vendor_id', 'status']);
            $table->index(['make_id', 'model_id', 'year']);
            $table->index(['availability_type', 'status']);
            $table->index(['city', 'state', 'country']);
            $table->index(['price', 'condition']);
            $table->index(['is_featured', 'created_at']);
            $table->index(['view_count', 'created_at']);
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
