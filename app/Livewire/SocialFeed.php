<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Post;
use App\Models\Reel;
use App\Models\User;
use App\Services\BookmarkService;
use App\Services\FollowService;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SocialFeed extends Component
{
    public string $activeTab = 'for_you'; // for_you | following | reels | shop

    public array $followStatuses = [];

    public ?int $activeCommentsPostId = null;

    public string $newComment = '';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->activeCommentsPostId = null;
    }

    public function toggleFollow(int $userId, FollowService $followService): void
    {
        $currentUser = auth()->user();

        if (! $currentUser instanceof User) {
            $this->redirectRoute('login');

            return;
        }

        if ($currentUser->id === $userId) {
            return;
        }

        $followedUser = User::findOrFail($userId);
        $follow = $followService->toggle($currentUser, $followedUser);

        if ($follow) {
            $this->followStatuses[$userId] = $follow->status;
        } else {
            unset($this->followStatuses[$userId]);
        }
    }

    public function toggleLike(int $postId): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        $post = Post::visibleTo($user)->findOrFail($postId);

        $existing = Like::query()
            ->where('likeable_type', Post::class)
            ->where('likeable_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Like::create([
                'likeable_type' => Post::class,
                'likeable_id' => $post->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function toggleBookmark(int $postId, BookmarkService $bookmarkService): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            $this->redirectRoute('login');

            return;
        }

        $post = Post::visibleTo($user)->findOrFail($postId);
        $bookmarkService->toggle($user, $post);
    }

    public function openComments(int $postId): void
    {
        Post::visibleTo(auth()->user())->findOrFail($postId);
        $this->activeCommentsPostId = $postId;
        $this->newComment = '';
    }

    public function closeComments(): void
    {
        $this->activeCommentsPostId = null;
        $this->newComment = '';
    }

    public function postComment(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to comment.');

            return;
        }

        if (! $this->activeCommentsPostId) {
            $this->addError('newComment', 'Choose a post before commenting.');

            return;
        }

        $post = Post::visibleTo($user)->findOrFail($this->activeCommentsPostId);

        $this->validate(['newComment' => 'required|string|max:500']);

        Comment::create([
            'commentable_type' => Post::class,
            'commentable_id' => $post->id,
            'user_id' => $user->id,
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $currentUser = auth()->user();
        $viewer = $currentUser instanceof User ? $currentUser : null;

        // ---------- Stories ----------
        $storyUsers = collect();

        if ($currentUser) {
            $storyUsers = User::with(['profile', 'activeStories'])
                ->whereHas('followers', function ($query) use ($currentUser) {
                    $query->where('follower_id', $currentUser->id)
                        ->where('follows.status', 'accepted');
                })
                ->whereHas('activeStories')
                ->where('status', true)
                ->withoutRole(['Admin', 'Super Admin'])
                ->take(15)
                ->get();
        }

        // ---------- Posts (feed tabs) ----------
        $postsQuery = Post::with([
            'user.profile',
            'user.activeStories',
            'product.images',
            'images',
            'comments' => fn ($q) => $q->latest()->limit(10)->with('user:id,name'),
        ])
            ->withCount(['likes as actual_likes_count'])
            ->visibleTo($viewer)
            ->whereHas('user', fn ($q) => $q->withoutRole(['Admin', 'Super Admin']));

        if ($viewer) {
            $postsQuery->withExists([
                'likes as is_liked_by_viewer' => fn ($query) => $query->where('user_id', $viewer->id),
                'bookmarks as is_bookmarked_by_viewer' => fn ($query) => $query->where('user_id', $viewer->id),
            ]);
        }

        if ($this->activeTab === 'following' && $currentUser) {
            $postsQuery->whereHas('user.followers', function ($query) use ($currentUser) {
                $query->where('follower_id', $currentUser->id)
                    ->where('follows.status', 'accepted');
            });
        } elseif ($this->activeTab === 'shop') {
            $postsQuery->whereNotNull('product_id');
        }

        $posts = $postsQuery->latest()->paginate(10);
        $this->followStatuses = $currentUser
            ? Follow::query()
                ->where('follower_id', $currentUser->id)
                ->whereIn('following_id', $posts->getCollection()->pluck('user_id')->unique())
                ->pluck('status', 'following_id')
                ->all()
            : [];

        // ---------- Reels tab ----------
        $reels = collect();
        if ($this->activeTab === 'reels') {
            $reels = Reel::query()
                ->visibleTo($viewer)
                ->with(['user.profile', 'product'])
                ->latest()
                ->take(8)
                ->get();
        }

        // ---------- Sidebar: Trending reels ----------
        $trendingReels = Reel::query()
            ->visibleTo($viewer)
            ->with(['user.profile', 'product'])
            ->latest()
            ->take(3)
            ->get();

        // ---------- Sidebar: Categories ----------
        $categories = Category::query()
            ->active()
            ->rootLevel()
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        return view('livewire.social-feed', [
            'storyUsers' => $storyUsers,
            'posts' => $posts,
            'reels' => $reels,
            'trendingReels' => $trendingReels,
            'categories' => $categories,
            'followStatuses' => $this->followStatuses,
        ]);
    }
}
