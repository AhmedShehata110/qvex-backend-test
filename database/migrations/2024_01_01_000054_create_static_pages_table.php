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
        Schema::create('static_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->datetime('published_at')->nullable();
            $table->string('template')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            // General fields
            $this->addGeneralFields($table);

            $table->index(['is_published', 'published_at']);
            $table->index('slug');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('static_pages');
    }
};
