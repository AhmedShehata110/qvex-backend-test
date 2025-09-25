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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->json('search_criteria');
            $table->boolean('email_alerts')->default(true);

            // General fields
            $this->addGeneralFields($table);

            $table->integer('alert_frequency')->default(24);
            $table->timestamp('last_alert_sent')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'is_active']);
            $table->index(['email_alerts', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
