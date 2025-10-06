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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', [
                'national_id', 'passport', 'driver_license',
                'utility_bill', 'bank_statement', 'other',
            ]);
            $table->string('document_number')->nullable();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $this->addGeneralFields($table);

            $table->index(['user_id', 'document_type']);
            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
