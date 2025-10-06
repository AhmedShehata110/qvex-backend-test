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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable field
            $table->string('slug')->unique();
            $table->string('country_origin')->nullable();

            // General fields
            $this->addGeneralFields($table);

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
