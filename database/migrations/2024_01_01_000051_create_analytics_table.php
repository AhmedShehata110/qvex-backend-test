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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('metric');
            $table->decimal('value', 15, 2)->default(0);
            $table->date('date');
            $table->json('metadata')->nullable();
            $table->timestamps();

            // General fields
            $this->addGeneralFields($table);

            $table->index(['type', 'date']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('metric');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
