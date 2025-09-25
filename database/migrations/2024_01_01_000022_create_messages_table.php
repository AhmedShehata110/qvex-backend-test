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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('thread_id');
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->foreignId('vehicle_id')->nullable()->constrained();
            $table->text('message');
            $table->enum('type', ['text', 'image', 'file', 'system', 'offer']);
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_system_message')->default(false);
            $table->timestamps();

            // General fields
            $this->addGeneralFields($table);

            $table->index(['thread_id', 'created_at']);
            $table->index(['sender_id', 'receiver_id']);
            $table->index(['receiver_id', 'read_at']);
            $table->index(['vehicle_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
