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
        Schema::create('vendor_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'manager', 'sales_agent', 'support']);
            $table->json('permissions')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $this->addGeneralFields($table);
            $table->timestamps();

            $table->unique(['vendor_id', 'user_id']);
            $table->index(['vendor_id', 'role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_staff');
    }
};
