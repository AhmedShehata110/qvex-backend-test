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
        Schema::create('vehicle_trims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('vehicle_models')->onDelete('cascade');
            $table->json('name'); // Translatable field
            $table->integer('year');
            $table->string('engine_size')->nullable();
            $table->enum('fuel_type', ['gasoline', 'diesel', 'hybrid', 'electric', 'cng', 'lpg']);
            $table->enum('transmission', ['manual', 'automatic', 'cvt', 'dual_clutch']);
            $table->string('drivetrain')->nullable();
            $table->integer('horsepower')->nullable();
            $table->decimal('fuel_consumption_city', 5, 2)->nullable();
            $table->decimal('fuel_consumption_highway', 5, 2)->nullable();
            $table->integer('seating_capacity')->nullable();

            // General fields
            $this->addGeneralFields($table);

            $table->timestamps();

            $table->index(['model_id', 'year', 'is_active']);
            $table->index(['fuel_type', 'transmission']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_trims');
    }
};
