<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;

trait MigrationTrait
{
    public function addGeneralFields(Blueprint $table): void
    {
        $table->boolean('is_active')->comment('1 = active , 0 = inactive')->default(1);
        $table->unsignedBigInteger('added_by_id')->nullable();
        $table->foreign('added_by_id')->references('id')->on('users')->onDelete('set null');
        $table->softDeletes();
    }

    public function dropGeneralFields(Blueprint $table): void
    {
        $table->dropForeign(['added_by_id']);
        $table->dropColumn('added_by_id');
        $table->dropColumn('is_active');
        $table->dropSoftDeletes();
    }
}
