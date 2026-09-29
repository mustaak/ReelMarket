<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Reel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ReelsPage extends Component
{
    /** @var array<int> Reel ids the current user has liked */
    public array $likedReelIds = [];

    /** @var array<int> User ids the current user follows */
    public array $followingIds = [];

    public ?int $activeCommentsReelId = null;

    public string $newComment = '';

    public function mount(): void
    {
        if (auth()->check()) {
            $this->likedReelIds = Like::query()
                ->where('user_id', auth()->id())
                ->where('likeable_type', Reel::class)
                ->pluck('likeable_id')
                ->all();

            $this->followingIds = Follow::query()
                ->where('follower_id', auth()->id())
                ->pluck('following_id')
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

    public function toggleFollow(int $userId): void
    {
        if (! auth()->check()) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to follow people.');

            return;
        }

        if ($userId === auth()->id()) {
            return;
        }

        $existing = Follow::query()
            ->where('follower_id', auth()->id())
            ->where('following_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->followingIds = array_values(array_diff($this->followingIds, [$userId]));
        } else {
            Follow::create(['follower_id' => auth()->id(), 'following_id' => $userId]);
            $this->followingIds[] = $userId;
        }
        // follower/following counts are kept in sync by FollowObserver
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
        Reel::whereKey($reelId)->increment('views_count');
    }

    public function render()
    {
        $reels = Reel::query()
            ->where('status', 'published')
            ->with([
                'user:id,name',
                'user.profile:id,user_id,profile_picture',
                'product:id,name,slug,price,sale_price',
                'comments' => fn ($q) => $q->latest()->limit(20)->with('user:id,name'),
            ])
            ->latest('id')
            ->limit(20)
            ->get();

        return view('livewire.reels-page', compact('reels'));
    }
}