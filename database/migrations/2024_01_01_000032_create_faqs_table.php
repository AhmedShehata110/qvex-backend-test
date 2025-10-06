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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->json('question'); // Translatable field
            $table->json('answer'); // Translatable field
            $table->string('category')->default('general');

            // General fields
            $this->addGeneralFields($table);

            $table->integer('sort_order')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->index(['category', 'is_active', 'sort_order']);
            // Note: FULLTEXT index removed due to MySQL limitation with JSON columns
            // $table->fullText(['question', 'answer']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
