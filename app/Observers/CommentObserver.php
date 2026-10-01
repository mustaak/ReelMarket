<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\Reel;
use App\Models\User;
use App\Notifications\SocialActivityNotification;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        $commentable = $comment->commentable;

        if (! $commentable) {
            return;
        }

        $commentable->comments_count = (int) ($commentable->comments_count ?? 0) + 1;
        $commentable->saveQuietly();

        $recipient = $commentable->user;
        $actor = $comment->user;

        if ($recipient instanceof User && $actor instanceof User && ! $recipient->is($actor)) {
            $contentType = $commentable instanceof Reel ? 'reel' : 'post';
            $url = $commentable instanceof Reel
                ? route('reels.index', ['reel' => $commentable->getKey()])
                : route('home', ['post' => $commentable->getKey()]).'#post-'.$commentable->getKey();

            $recipient->notify(new SocialActivityNotification(
                $actor,
                'comment',
                "commented on your {$contentType}.",
                $url,
            ));
        }
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        $commentable = $comment->commentable;

        if (! $commentable) {
            return;
        }

        $commentable->comments_count = max(0, (int) ($commentable->comments_count ?? 0) - 1);
        $commentable->saveQuietly();
    }

    /**
     * Handle the Comment "restored" event.
     */
    public function restored(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "force deleted" event.
     */
    public function forceDeleted(Comment $comment): void
    {
        //
    }
}
