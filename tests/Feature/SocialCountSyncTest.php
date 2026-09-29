<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialCountSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_like_and_comment_counts_stay_synced_without_dropping_below_zero(): void
    {
        $user = User::factory()->create();
        $post = Post::create([
            'user_id' => $user->id,
            'content' => 'This is a test post.',
            'visibility' => 'public',
            'status' => 'published',
            'likes_count' => 0,
            'comments_count' => 0,
        ]);

        $this->assertSame(0, $post->fresh()->likes_count);
        $this->assertSame(0, $post->fresh()->comments_count);

        Like::create([
            'likeable_type' => Post::class,
            'likeable_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $this->assertSame(1, $post->fresh()->likes_count);

        $like = Like::where('likeable_type', Post::class)
            ->where('likeable_id', $post->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $like->delete();

        $this->assertSame(0, $post->fresh()->likes_count);

        Comment::create([
            'commentable_type' => Post::class,
            'commentable_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Great post.',
        ]);

        $this->assertSame(1, $post->fresh()->comments_count);

        $comment = Comment::where('commentable_type', Post::class)
            ->where('commentable_id', $post->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $comment->delete();

        $this->assertSame(0, $post->fresh()->comments_count);
    }
}
