<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Reel;
use App\Models\User;
use App\Models\ReelView;
use App\Services\FollowService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;


#[Layout('components.layouts.app')]
class ReelsPage extends Component
{
    /** @var array<int> Reel ids the current user has liked */
    public array $likedReelIds = [];

    /** @var array<int, string> Follow status indexed by followed user id */
    public array $followStatuses = [];

    public ?int $focusedReelId = null;

    public ?int $activeCommentsReelId = null;

    public string $newComment = '';

    public function mount(): void
    {
        $focusedReelId = request()->integer('reel');

        

        if ($focusedReelId > 0) {
            $this->focusedReelId = $focusedReelId;
        }

        if (auth()->check()) {
            $this->likedReelIds = Like::query()
                ->where('user_id', auth()->id())
                ->where('likeable_type', Reel::class)
                ->pluck('likeable_id')
                ->all();

            $this->followStatuses = Follow::query()
                ->where('follower_id', auth()->id())
                ->pluck('status', 'following_id')
                ->all();
        }
    }

    public function toggleLike(int $reelId): void
    {
        if (! auth()->check()) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to like reels.');

            return;
        }

        $reel = Reel::findOrFail($reelId);

        $existing = Like::query()
            ->where('likeable_type', Reel::class)
            ->where('likeable_id', $reel->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            $existing->delete();
            $this->likedReelIds = array_values(array_diff($this->likedReelIds, [$reelId]));
        } else {
            Like::create([
                'likeable_type' => Reel::class,
                'likeable_id' => $reel->id,
                'user_id' => auth()->id(),
            ]);
            $this->likedReelIds[] = $reelId;
        }
        // Reel::likes_count is kept in sync by LikeObserver
    }

    public function toggleFollow(int $userId, FollowService $followService): void
    {
        if (! auth()->check()) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to follow people.');

            return;
        }

        if ($userId === auth()->id()) {
            return;
        }

        $follower = auth()->user();

        if (! $follower instanceof User) {
            return;
        }

        $followedUser = User::findOrFail($userId);
        $follow = $followService->toggle($follower, $followedUser);

        if ($follow) {
            $this->followStatuses[$userId] = $follow->status;
        } else {
            unset($this->followStatuses[$userId]);
        }
    }

    public function openComments(int $reelId): void
    {
        $this->activeCommentsReelId = $reelId;
    }

    public function closeComments(): void
    {
        $this->activeCommentsReelId = null;
        $this->newComment = '';
    }

    public function postComment(): void
    {
        if (! auth()->check()) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to comment.');

            return;
        }

        $this->validate(['newComment' => 'required|string|max:500']);

        Comment::create([
            'commentable_type' => Reel::class,
            'commentable_id' => $this->activeCommentsReelId,
            'user_id' => auth()->id(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        // comments_count is kept in sync by CommentObserver
    }

    #[On('reel-viewed')]
    public function markViewed(int $reelId): void
    {
        if (! auth()->check()) {
            return;
        }

        $view = ReelView::firstOrCreate([
            'reel_id' => $reelId,
            'user_id' => auth()->id(),
        ]);

        if ($view->wasRecentlyCreated) {
            Reel::whereKey($reelId)->increment('views_count');
        }
    }

    public function render()
    {
        $viewer = auth()->user();
        

        $reelsQuery = Reel::query()
            ->visibleTo($viewer instanceof User ? $viewer : null)
            ->with([
                'user:id,name',
                'user.profile:id,user_id,profile_picture',
                'product:id,name,slug,price,sale_price',
                'comments' => fn ($q) => $q->latest()->limit(20)->with('user:id,name'),
            ]);

        if ($this->focusedReelId) {
            $reelsQuery->whereKey($this->focusedReelId);
        } else {
            $reelsQuery->latest('id')->limit(20);
        }

        $reels = $reelsQuery->get();

        return view('livewire.reels-page', [
            'reels' => $reels,
            'followStatuses' => $this->followStatuses,
        ]);
    }
}
