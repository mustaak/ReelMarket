<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReelsPage;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReelsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_comments_drawer_opens_and_accepts_a_comment_for_a_visible_reel(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $viewer = User::factory()->create();
        $reel = Reel::query()->create([
            'user_id' => $viewer->id,
            'video_path' => 'reels/test-video.mp4',
            'status' => 'published',
        ]);

        $this->actingAs($viewer);

        Livewire::test(ReelsPage::class)
            ->call('openComments', $reel->id)
            ->assertSee('Comments')
            ->assertSee('Write a comment...')
            ->set('newComment', 'Great reel!')
            ->call('postComment')
            ->assertSee('Great reel!');

        $this->assertDatabaseHas('comments', [
            'commentable_type' => Reel::class,
            'commentable_id' => $reel->id,
            'user_id' => $viewer->id,
            'content' => 'Great reel!',
        ]);
    }

    public function test_reels_render_viewer_engagement_and_update_it_after_actions(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $viewer = User::factory()->create();
        $reel = Reel::query()->create([
            'user_id' => $viewer->id,
            'video_path' => 'reels/test-video.mp4',
            'status' => 'published',
        ]);

        $this->actingAs($viewer);

        Livewire::test(ReelsPage::class)
            ->assertViewHas('reels', function (Collection $reels) use ($reel): bool {
                $renderedReel = $reels->firstWhere('id', $reel->id);

                return $renderedReel instanceof Reel
                    && ! (bool) $renderedReel->is_liked_by_viewer
                    && ! (bool) $renderedReel->is_bookmarked_by_viewer;
            })
            ->call('toggleLike', $reel->id)
            ->assertViewHas('reels', function (Collection $reels) use ($reel): bool {
                $renderedReel = $reels->firstWhere('id', $reel->id);

                return $renderedReel instanceof Reel
                    && (bool) $renderedReel->is_liked_by_viewer;
            })
            ->call('toggleBookmark', $reel->id)
            ->assertViewHas('reels', function (Collection $reels) use ($reel): bool {
                $renderedReel = $reels->firstWhere('id', $reel->id);

                return $renderedReel instanceof Reel
                    && (bool) $renderedReel->is_bookmarked_by_viewer;
            });

        $this->assertDatabaseHas('likes', [
            'likeable_type' => Reel::class,
            'likeable_id' => $reel->id,
            'user_id' => $viewer->id,
        ]);
        $this->assertDatabaseHas('bookmarks', [
            'bookmarkable_type' => Reel::class,
            'bookmarkable_id' => $reel->id,
            'user_id' => $viewer->id,
        ]);
    }
}
