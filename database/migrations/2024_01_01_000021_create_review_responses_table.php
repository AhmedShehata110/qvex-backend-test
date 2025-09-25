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
        Schema::create('review_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('responder_id')->constrained('users');
            $table->text('response');
            $table->enum('responder_type', ['vendor', 'admin']);
            $table->timestamps();
            $table->index(['review_id', 'responder_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_responses');
    }
};
