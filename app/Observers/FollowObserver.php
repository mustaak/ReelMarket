<?php

namespace App\Observers;

use App\Models\Follow;

class FollowObserver
{
    /**
     * Handle the Follow "created" event.
     */
    public function created(Follow $follow): void
    {
        $this->synchronizeCounts($follow);
    }

    /**
     * Handle the Follow "updated" event.
     */
    public function updated(Follow $follow): void
    {
        if ($follow->wasChanged('status')) {
            $this->synchronizeCounts($follow);
        }
    }

    /**
     * Handle the Follow "deleted" event.
     */
    public function deleted(Follow $follow): void
    {
        $this->synchronizeCounts($follow);
    }

    /**
     * Handle the Follow "restored" event.
     */
    public function restored(Follow $follow): void
    {
        //
    }

    /**
     * Handle the Follow "force deleted" event.
     */
    public function forceDeleted(Follow $follow): void
    {
        //
    }

    private function synchronizeCounts(Follow $follow): void
    {
        $followingProfile = $follow->following->profile()->firstOrCreate([]);
        $followerProfile = $follow->follower->profile()->firstOrCreate([]);

        $followingProfile->update([
            'followers_count' => Follow::query()
                ->where('following_id', $follow->following_id)
                ->where('status', 'accepted')
                ->count(),
        ]);
        $followerProfile->update([
            'following_count' => Follow::query()
                ->where('follower_id', $follow->follower_id)
                ->whereIn('status', ['accepted', 'pending'])
                ->count(),
        ]);
    }
}
