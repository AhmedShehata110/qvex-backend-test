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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Translatable field
            $table->string('slug')->unique();
            $table->json('excerpt'); // Translatable field
            $table->json('content'); // Translatable field
            $table->string('featured_image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();
            $table->integer('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->constrained('users');
            $table->timestamps();

            // General fields
            $this->addGeneralFields($table);

            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'published_at']);
            $table->index(['author_id', 'status']);
            // Note: FULLTEXT index removed due to MySQL limitation with JSON columns
            // $table->fullText(['title', 'excerpt', 'content']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
