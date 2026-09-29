<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

class HomeFeed extends Component
{
    public array $likedPostIds = [];

    public ?int $activeCommentsPostId = null;

    public string $newComment = '';

    public function mount(): void
    {
        if (auth()->check()) {
            $this->likedPostIds = Like::query()
                ->where('user_id', auth()->id())
                ->where('likeable_type', Post::class)
                ->pluck('likeable_id')
                ->all();
        }
    }

    public function toggleFollow($userId)
    {
        $currentUser = auth()->user();

        if (! $currentUser) {
            return $this->redirectRoute('login');
        }

        if ($currentUser->id !== (int) $userId) {
            $currentUser->following()->toggle($userId);
        }
    }

    public function toggleLike($postId)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->redirectRoute('login');
        }

        $post = Post::findOrFail($postId);

        $existing = Like::query()
            ->where('likeable_type', Post::class)
            ->where('likeable_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->likedPostIds = array_values(array_diff($this->likedPostIds, [$postId]));
        } else {
            Like::create([
                'likeable_type' => Post::class,
                'likeable_id' => $post->id,
                'user_id' => $user->id,
            ]);
            $this->likedPostIds[] = (int) $postId;
        }
    }

    public function openComments(int $postId): void
    {
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
        if (! auth()->check()) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to comment.');

            return;
        }

        $this->validate(['newComment' => 'required|string|max:500']);

        Comment::create([
            'commentable_type' => Post::class,
            'commentable_id' => $this->activeCommentsPostId,
            'user_id' => auth()->id(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $currentUser = auth()->user();

        $storyUsers = collect();

        if ($currentUser) {
            $storyUsers = User::with('profile')
                ->whereHas('followers', function ($query) use ($currentUser) {
                    $query->where('follower_id', $currentUser->id);
                })
                ->where('status', true)
                ->withoutRole(['Admin', 'Super Admin'])
                ->take(10)
                ->get();
        }

        $posts = Post::with([
            'user.profile',
            'product',
            'likes',
            'images',
            'comments' => fn ($q) => $q->latest()->limit(10)->with('user:id,name'),
            'product.images',
        ])
            ->whereHas('user', function ($query) {
                $query->withoutRole(['Admin', 'Super Admin']);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.home-feed', [
            'storyUsers' => $storyUsers,
            'posts' => $posts,
        ]);
    }
}