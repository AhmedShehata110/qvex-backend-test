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
        Schema::create('vehicle_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->enum('inquiry_type', ['general', 'test_drive', 'price_negotiation', 'inspection']);
            $table->string('subject');
            $table->text('message');
            $table->json('contact_preferences')->nullable();
            $table->string('preferred_contact_time')->nullable();
            $table->enum('status', ['new', 'contacted', 'in_progress', 'closed'])->default('new');
            $table->text('vendor_notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->index(['vehicle_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $this->addGeneralFields($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_inquiries');
    }
};
