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
        Schema::create('vendor_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained();
            $table->decimal('amount_paid', 10, 2);
            $table->string('currency', 3);
            $table->enum('status', ['active', 'cancelled', 'expired', 'suspended']);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('cancelled_at')->nullable();
            $table->integer('listings_used')->default(0);
            $table->integer('featured_listings_used')->default(0);
            $table->boolean('auto_renewal')->default(true);
            $table->string('payment_reference')->nullable();
            // General fields
            $this->addGeneralFields($table);
            $table->timestamps();
            $table->index(['vendor_id', 'status']);
            $table->index(['ends_at', 'status']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_subscriptions');
    }
};
