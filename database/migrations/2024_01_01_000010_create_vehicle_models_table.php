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
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('make_id')->constrained('vehicle_makes')->onDelete('cascade');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('slug');
            $table->integer('year_start');
            $table->integer('year_end')->nullable();
            $table->enum('body_type', [
                'sedan', 'suv', 'hatchback', 'coupe', 'convertible',
                'wagon', 'pickup', 'van', 'truck', 'motorcycle', 'other',
            ])->nullable();

            // General fields
            $this->addGeneralFields($table);

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['make_id', 'slug']);
            $table->index(['make_id', 'is_active']);
            $table->index(['body_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
    }
};
