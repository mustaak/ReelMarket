<?php

namespace App\Observers;

use App\Models\Like;
use App\Models\Reel;
use App\Models\User;
use App\Notifications\SocialActivityNotification;

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

        $recipient = $likeable->user;
        $actor = $like->user;

        if ($recipient instanceof User && $actor instanceof User && ! $recipient->is($actor)) {
            $contentType = $likeable instanceof Reel ? 'reel' : 'post';
            $recipient->notify(new SocialActivityNotification(
                $actor,
                'like',
                "liked your {$contentType}.",
                route('home'),
            ));
        }
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
