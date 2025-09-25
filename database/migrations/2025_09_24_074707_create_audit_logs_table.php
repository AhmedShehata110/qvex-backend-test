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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // The model class name
            $table->unsignedBigInteger('model_id'); // The model's primary key
            $table->string('event'); // created, updated, deleted, restored
            $table->json('old_values')->nullable(); // Previous attribute values
            $table->json('new_values')->nullable(); // New attribute values
            $table->unsignedBigInteger('user_id')->nullable(); // User who performed the action
            $table->string('user_type')->nullable(); // User model type (for polymorphic)
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable(); // Request URL
            $table->string('method')->nullable(); // HTTP method
            $table->json('request_data')->nullable(); // Request payload if needed
            $table->string('batch_uuid')->nullable(); // For grouping related changes
            $table->text('tags')->nullable(); // Comma-separated tags for categorization
            $table->timestamp('occurred_at'); // When the event occurred
            $table->timestamps();

            // Indexes for better performance
            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'user_type']);
            $table->index(['event', 'occurred_at']);
            $table->index('batch_uuid');
            $table->index('occurred_at');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
