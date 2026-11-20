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
        Schema::table('messages', function (Blueprint $table) {
            $table->uuid('call_id')->nullable()->unique();
            $table->string('call_type', 10)->nullable();
            $table->string('call_status', 20)->nullable();
            $table->unsignedInteger('call_duration_seconds')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropUnique(['call_id']);
            $table->dropColumn([
                'call_id',
                'call_type',
                'call_status',
                'call_duration_seconds',
            ]);
        });
    }
};
