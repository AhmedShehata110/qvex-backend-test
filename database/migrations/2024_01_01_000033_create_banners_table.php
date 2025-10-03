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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Translatable field
            $table->json('description')->nullable(); // Translatable field
            $table->string('image');
            $table->string('image_mobile')->nullable();
            $table->string('link_url')->nullable();
            $table->json('link_text')->nullable(); // Translatable field
            $table->enum('position', ['hero', 'sidebar', 'footer', 'popup', 'in_content']);
            $table->enum('type', ['promotional', 'informational', 'vendor_ad']);
            $table->json('targeting')->nullable();

            // General fields
            $this->addGeneralFields($table);

            $table->integer('sort_order')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->datetime('starts_at');
            $table->datetime('expires_at')->nullable();
            $table->timestamps();
            $table->index(['position', 'is_active', 'sort_order']);
            $table->index(['starts_at', 'expires_at', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
