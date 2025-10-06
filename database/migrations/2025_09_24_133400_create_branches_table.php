<?php

use App\Traits\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use MigrationTrait;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable field
            $table->json('description')->nullable(); // Translatable field
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->json('address')->nullable(); // Translatable field
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('manager_name', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('working_hours')->nullable(); // Store as JSON: {"monday": "09:00-17:00", etc.}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $this->addGeneralFields($table);
            $table->index(['vendor_id', 'is_active']);
            $table->index(['country_id', 'city_id']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
