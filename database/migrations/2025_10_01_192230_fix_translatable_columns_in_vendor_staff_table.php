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
            $table->text('position')->change();
            $table->text('department')->change();
            $table->text('notes')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_staff', function (Blueprint $table) {
            $table->string('position', 100)->change();
            $table->string('department', 100)->change();
            $table->string('notes', 1000)->change();
        });
    }
};
