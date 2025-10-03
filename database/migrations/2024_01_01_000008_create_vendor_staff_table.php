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
            $table->text('position')->nullable();
            $table->text('department')->nullable();
            $table->enum('employment_status', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->date('hire_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('employee_id', 50)->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamp('last_active_at')->nullable();
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
