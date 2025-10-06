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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable field
            $table->string('code', 2)->unique(); // ISO country code
            $table->string('currency_code', 3)->nullable();
            $table->string('phone_code', 5)->nullable();
            $table->timestamps();
            $table->boolean('is_active')->default(true);
            $this->addGeneralFields($table);
            $table->index(['code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
