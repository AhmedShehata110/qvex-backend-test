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
        Schema::create('rental_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('rental_days');
            $table->decimal('daily_rate', 8, 2);
            $table->decimal('weekly_rate', 8, 2)->nullable();
            $table->decimal('monthly_rate', 8, 2)->nullable();
            $table->decimal('security_deposit', 8, 2);
            $table->decimal('mileage_limit_per_day', 8, 2)->nullable();
            $table->decimal('extra_mileage_rate', 6, 2)->nullable();
            $table->integer('pickup_mileage')->nullable();
            $table->integer('return_mileage')->nullable();
            $table->text('pickup_location');
            $table->text('return_location');
            $table->time('pickup_time')->nullable();
            $table->time('return_time')->nullable();
            $table->enum('fuel_policy', ['full_to_full', 'full_to_empty', 'same_to_same']);
            $table->text('terms_conditions')->nullable();
            $table->text('special_instructions')->nullable();
            $table->json('damage_report_pickup')->nullable();
            $table->json('damage_report_return')->nullable();
            $table->decimal('damage_charges', 8, 2)->default(0);
            $table->decimal('late_return_charges', 8, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled', 'extended'])->default('active');
            $table->timestamp('pickup_confirmed_at')->nullable();
            $table->timestamp('return_confirmed_at')->nullable();
            $table->timestamps();
            $table->index(['start_date', 'end_date']);
            $table->index(['status', 'end_date']);
            $this->addGeneralFields($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_agreements');
    }
};
