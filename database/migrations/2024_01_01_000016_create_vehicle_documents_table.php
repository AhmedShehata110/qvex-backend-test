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
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', [
                'registration', 'insurance', 'inspection', 'service_record',
                'ownership_transfer', 'loan_clearance', 'other',
            ]);
            $table->json('title'); // Translatable field
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $this->addGeneralFields($table);
            $table->index(['vehicle_id', 'document_type']);
            $table->index(['is_verified', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
