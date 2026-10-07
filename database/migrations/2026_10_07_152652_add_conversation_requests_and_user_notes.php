<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A conversation is a "request" for a user until accepted_at is set on their row
        Schema::table('conversation_user', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable();
        });

        // 24-hour notes shown above avatars in the inbox
        Schema::table('users', function (Blueprint $table) {
            $table->string('note', 60)->nullable();
            $table->timestamp('note_expires_at')->nullable();
        });

        // Existing conversations are treated as already accepted
        DB::table('conversation_user')->update(['accepted_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('conversation_user', function (Blueprint $table) {
            $table->dropColumn('accepted_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['note', 'note_expires_at']);
        });
    }
};