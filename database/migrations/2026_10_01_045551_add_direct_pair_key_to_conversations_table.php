<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('direct_pair_key', 64)->nullable()->unique();
        });

        $directConversations = DB::table('conversation_user')
            ->join('conversations', 'conversations.id', '=', 'conversation_user.conversation_id')
            ->select('conversation_user.conversation_id')
            ->selectRaw('MIN(conversation_user.user_id) as user_one')
            ->selectRaw('MAX(conversation_user.user_id) as user_two')
            ->selectRaw('MAX(conversations.updated_at) as latest_activity')
            ->groupBy('conversation_user.conversation_id')
            ->havingRaw('COUNT(DISTINCT conversation_user.user_id) = 2')
            ->orderByDesc('latest_activity')
            ->get()
            ->groupBy(fn (object $conversation): string => $conversation->user_one.':'.$conversation->user_two);

        foreach ($directConversations as $pairKey => $conversations) {
            $primaryId = (int) $conversations->first()->conversation_id;
            $duplicateIds = $conversations->pluck('conversation_id')
                ->map(fn ($id): int => (int) $id)
                ->reject(fn (int $id): bool => $id === $primaryId)
                ->all();

            if ($duplicateIds !== []) {
                DB::table('messages')
                    ->whereIn('conversation_id', $duplicateIds)
                    ->update(['conversation_id' => $primaryId]);

                DB::table('conversation_user')
                    ->whereIn('conversation_id', $duplicateIds)
                    ->delete();

                DB::table('conversations')->whereIn('id', $duplicateIds)->delete();
            }

            DB::table('conversations')
                ->where('id', $primaryId)
                ->update(['direct_pair_key' => $pairKey]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique(['direct_pair_key']);
            $table->dropColumn('direct_pair_key');
        });
    }
};
