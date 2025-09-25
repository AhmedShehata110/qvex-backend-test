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
        Schema::create('vehicle_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('slug')->unique();
            $table->enum('category', [
                'safety', 'technology', 'comfort', 'performance',
                'exterior', 'interior', 'audio', 'other',
            ]);
            $table->string('icon')->nullable();
            $table->boolean('is_premium')->default(false);

            // General fields
            $this->addGeneralFields($table);

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['is_premium', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_features');
    }
};
