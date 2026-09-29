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
        $follow->following->profile()->increment('followers_count');
        $follow->follower->profile()->increment('following_count');
    }

    /**
     * Handle the Follow "updated" event.
     */
    public function updated(Follow $follow): void
    {
        //
    }

    /**
     * Handle the Follow "deleted" event.
     */
    public function deleted(Follow $follow): void
    {
        $follow->following->profile()->decrement('followers_count');
        $follow->follower->profile()->decrement('following_count');
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
}
