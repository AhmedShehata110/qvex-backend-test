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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->string('payment_method');
            $table->string('payment_reference')->unique();
            $table->string('gateway_transaction_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded']);
            $table->enum('type', ['payment', 'refund', 'partial_refund']);
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->text('failure_reason')->nullable();
            $table->decimal('gateway_fee', 8, 2)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // General fields
            $this->addGeneralFields($table);

            $table->index(['transaction_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['gateway_transaction_id']);
            $table->index(['payment_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
