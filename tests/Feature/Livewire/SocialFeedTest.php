<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SocialFeed;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SocialFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_renders_viewer_engagement_and_updates_it_after_actions(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $viewer = User::factory()->create();
        $post = Post::query()->create([
            'user_id' => $viewer->id,
            'content' => 'Feed post for engagement state',
            'visibility' => 'public',
            'status' => 'published',
        ]);

        $this->actingAs($viewer);

        $component = Livewire::test(SocialFeed::class)
            ->assertViewHas('posts', function (LengthAwarePaginator $posts) use ($post): bool {
                $renderedPost = $posts->getCollection()->firstWhere('id', $post->id);

                return $renderedPost instanceof Post
                    && ! (bool) $renderedPost->is_liked_by_viewer
                    && ! (bool) $renderedPost->is_bookmarked_by_viewer
                    && (int) $renderedPost->actual_likes_count === 0;
            })
            ->call('toggleLike', $post->id)
            ->assertViewHas('posts', function (LengthAwarePaginator $posts) use ($post): bool {
                $renderedPost = $posts->getCollection()->firstWhere('id', $post->id);

                return $renderedPost instanceof Post
                    && (bool) $renderedPost->is_liked_by_viewer
                    && (int) $renderedPost->actual_likes_count === 1;
            })
            ->call('toggleBookmark', $post->id)
            ->assertViewHas('posts', function (LengthAwarePaginator $posts) use ($post): bool {
                $renderedPost = $posts->getCollection()->firstWhere('id', $post->id);

                return $renderedPost instanceof Post
                    && (bool) $renderedPost->is_bookmarked_by_viewer;
            });

        $this->assertDatabaseHas('likes', [
            'likeable_type' => Post::class,
            'likeable_id' => $post->id,
            'user_id' => $viewer->id,
        ]);
        $this->assertDatabaseHas('bookmarks', [
            'bookmarkable_type' => Post::class,
            'bookmarkable_id' => $post->id,
            'user_id' => $viewer->id,
        ]);
    }

    public function test_following_tab_only_renders_posts_from_accepted_follows(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $viewer = User::factory()->create();
        $followedUser = User::factory()->create();
        $otherUser = User::factory()->create();
        $followedPost = Post::query()->create([
            'user_id' => $followedUser->id,
            'content' => 'Post from followed account',
            'visibility' => 'public',
            'status' => 'published',
        ]);
        $otherPost = Post::query()->create([
            'user_id' => $otherUser->id,
            'content' => 'Post from unrelated account',
            'visibility' => 'public',
            'status' => 'published',
        ]);
        Follow::query()->create([
            'follower_id' => $viewer->id,
            'following_id' => $followedUser->id,
            'status' => 'accepted',
        ]);

        $this->actingAs($viewer);

        Livewire::test(SocialFeed::class)
            ->set('activeTab', 'following')
            ->assertSee($followedPost->content)
            ->assertDontSee($otherPost->content);
    }
}
