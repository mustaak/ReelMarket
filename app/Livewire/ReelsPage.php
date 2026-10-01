<?php

namespace App\Livewire;

use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Reel;
use App\Models\ReelView;
use App\Models\User;
use App\Services\BookmarkService;
use App\Services\FollowService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ReelsPage extends Component
{
    /** @var array<int> Reel ids the current user has liked */
    public array $likedReelIds = [];

    /** @var array<int> Reel ids saved by the current user */
    public array $bookmarkedReelIds = [];

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

            $this->bookmarkedReelIds = Bookmark::query()
                ->where('user_id', auth()->id())
                ->where('bookmarkable_type', Reel::class)
                ->pluck('bookmarkable_id')
                ->map(fn ($id) => (int) $id)
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

        $reel = Reel::visibleTo(auth()->user())->findOrFail($reelId);

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

    public function toggleBookmark(int $reelId, BookmarkService $bookmarkService): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to save reels.');

            return;
        }

        $reel = Reel::visibleTo($user)->findOrFail($reelId);
        $isBookmarked = $bookmarkService->toggle($user, $reel);

        if ($isBookmarked) {
            $this->bookmarkedReelIds[] = $reel->id;

            return;
        }

        $this->bookmarkedReelIds = array_values(array_diff($this->bookmarkedReelIds, [$reel->id]));
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
        Reel::visibleTo(auth()->user())->findOrFail($reelId);
        $this->activeCommentsReelId = $reelId;
    }

    public function closeComments(): void
    {
        $this->activeCommentsReelId = null;
        $this->newComment = '';
    }

    public function postComment(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to comment.');

            return;
        }

        if (! $this->activeCommentsReelId) {
            $this->addError('newComment', 'Choose a reel before commenting.');

            return;
        }

        $reel = Reel::visibleTo($user)->findOrFail($this->activeCommentsReelId);

        $this->validate(['newComment' => 'required|string|max:500']);

        Comment::create([
            'commentable_type' => Reel::class,
            'commentable_id' => $reel->id,
            'user_id' => $user->id,
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        // comments_count is kept in sync by CommentObserver
    }

    #[On('reel-viewed')]
    public function markViewed(int $reelId): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $reel = Reel::visibleTo($user)->findOrFail($reelId);

        $view = ReelView::firstOrCreate([
            'reel_id' => $reel->id,
            'user_id' => $user->id,
        ]);

        if ($view->wasRecentlyCreated) {
            $reel->increment('views_count');
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
            'bookmarkedReelIds' => $this->bookmarkedReelIds,
        ]);
    }
}
