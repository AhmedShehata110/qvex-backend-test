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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('locale', 5)->default('en');
            $table->string('timezone', 50)->default('UTC');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('user_type', ['admin', 'user'])->default('user');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->json('two_factor_recovery_codes')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // General fields (is_active, added_by_id, softDeletes)
            $this->addGeneralFields($table);

            $table->index(['email', 'phone']);
            $table->index('is_active');
            $table->index('user_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
