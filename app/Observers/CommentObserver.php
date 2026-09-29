<?php

namespace App\Observers;

use App\Models\Comment;

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
