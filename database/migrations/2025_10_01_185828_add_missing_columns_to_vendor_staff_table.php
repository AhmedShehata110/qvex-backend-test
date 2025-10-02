<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vendor_staff', function (Blueprint $table) {
            $table->string('position', 100)->nullable();
            $table->string('department', 100)->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_staff', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'department', 
                'employment_status',
                'hire_date',
                'salary',
                'commission_rate',
                'phone',
                'email',
                'emergency_contact_name',
                'emergency_contact_phone',
                'address',
                'employee_id',
                'notes',
                'last_active_at'
            ]);
        });
    }
};
