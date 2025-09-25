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
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hex_code')->nullable();
            $table->json('rgb_value')->nullable();
            $table->string('type')->default('exterior');
            $table->boolean('is_metallic')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // General fields
            $this->addGeneralFields($table);

            $table->index(['type', 'is_popular']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
