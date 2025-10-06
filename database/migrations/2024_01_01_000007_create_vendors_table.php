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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('business_name'); // Translatable field
            $table->string('slug')->unique();
            $table->json('description')->nullable(); // Translatable field
            $table->string('registration_number')->unique();
            $table->string('tax_id')->unique()->nullable();
            $table->string('trade_license')->nullable();
            $table->enum('vendor_type', ['dealership', 'rental_company', 'individual', 'service_center']);
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('pending');
            $table->json('business_hours')->nullable();
            $table->json('services_offered')->nullable();
            $table->string('website')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->integer('total_sales')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->date('subscription_expires_at')->nullable();
            $table->timestamps();

            // General fields
            $this->addGeneralFields($table);

            $table->index(['status', 'vendor_type']);
            $table->index(['is_featured', 'is_verified']);
            $table->index('rating_average');
            // Note: FULLTEXT index removed due to MySQL limitation with JSON columns
            // $table->fullText(['business_name', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
