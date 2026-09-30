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
        if ($follow->status === 'accepted') {
            $this->changeCounts($follow, 1);
        }
    }

    /**
     * Handle the Follow "updated" event.
     */
    public function updated(Follow $follow): void
    {
        if ($follow->wasChanged('status') && $follow->status === 'accepted') {
            $this->changeCounts($follow, 1);
        }
    }

    /**
     * Handle the Follow "deleted" event.
     */
    public function deleted(Follow $follow): void
    {
        if ($follow->status === 'accepted') {
            $this->changeCounts($follow, -1);
        }
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

    private function changeCounts(Follow $follow, int $amount): void
    {
        $followingProfile = $follow->following->profile()->firstOrCreate([]);
        $followerProfile = $follow->follower->profile()->firstOrCreate([]);

        if ($amount > 0) {
            $followingProfile->increment('followers_count', $amount);
            $followerProfile->increment('following_count', $amount);

            return;
        }

        $followingProfile->decrement('followers_count', abs($amount));
        $followerProfile->decrement('following_count', abs($amount));
    }
}
