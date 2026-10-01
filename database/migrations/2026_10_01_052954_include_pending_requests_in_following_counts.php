<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('user_profiles')
            ->select(['id', 'user_id'])
            ->orderBy('id')
            ->chunkById(200, function ($profiles): void {
                foreach ($profiles as $profile) {
                    DB::table('user_profiles')
                        ->where('id', $profile->id)
                        ->update([
                            'following_count' => DB::table('follows')
                                ->where('follower_id', $profile->user_id)
                                ->whereIn('status', ['accepted', 'pending'])
                                ->count(),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
