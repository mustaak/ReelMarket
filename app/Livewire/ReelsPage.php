<?php

namespace App\Livewire;

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
        } else {
            Like::create([
                'likeable_type' => Reel::class,
                'likeable_id' => $reel->id,
                'user_id' => auth()->id(),
            ]);
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
        $bookmarkService->toggle($user, $reel);
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
        $this->newComment = '';
        $this->resetErrorBag('newComment');
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

        if ($viewer instanceof User) {
            $reelsQuery->withExists([
                'likes as is_liked_by_viewer' => fn ($query) => $query->where('user_id', $viewer->id),
                'bookmarks as is_bookmarked_by_viewer' => fn ($query) => $query->where('user_id', $viewer->id),
            ]);
        }

        if ($this->focusedReelId) {
            $reelsQuery->whereKey($this->focusedReelId);
        } else {
            $reelsQuery->latest('id')->limit(20);
        }

        $reels = $reelsQuery->get();
        $this->followStatuses = $viewer instanceof User
            ? Follow::query()
                ->where('follower_id', $viewer->id)
                ->whereIn('following_id', $reels->pluck('user_id')->unique())
                ->pluck('status', 'following_id')
                ->all()
            : [];

        return view('livewire.reels-page', [
            'reels' => $reels,
            'followStatuses' => $this->followStatuses,
            'activeCommentsReel' => $reels->firstWhere('id', $this->activeCommentsReelId),
        ]);
    }
}
