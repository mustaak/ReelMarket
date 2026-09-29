<?php

namespace App\Observers;

use App\Models\Like;

class LikeObserver
{
    /**
     * Handle the Like "created" event.
     */
    public function created(Like $like): void
    {
        $likeable = $like->likeable;

        if (! $likeable) {
            return;
        }

        $likeable->likes_count = (int) ($likeable->likes_count ?? 0) + 1;
        $likeable->saveQuietly();
    }

    /**
     * Handle the Like "updated" event.
     */
    public function updated(Like $like): void
    {
        //
    }

    /**
     * Handle the Like "deleted" event.
     */
    public function deleted(Like $like): void
    {
        $likeable = $like->likeable;

        if (! $likeable) {
            return;
        }

        $likeable->likes_count = max(0, (int) ($likeable->likes_count ?? 0) - 1);
        $likeable->saveQuietly();
    }

    /**
     * Handle the Like "restored" event.
     */
    public function restored(Like $like): void
    {
        //
    }

    /**
     * Handle the Like "force deleted" event.
     */
    public function forceDeleted(Like $like): void
    {
        //
    }
}
