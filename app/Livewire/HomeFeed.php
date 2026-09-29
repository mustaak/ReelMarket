<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Post;
use App\Models\User;

class HomeFeed extends Component
{
    // Follow / Unfollow User Toggle Logic
    public function toggleFollow($userId)
    {
        $currentUser = auth()->user();

        if (!$currentUser) {
            return $this->redirectRoute('login');
        }

        if ($currentUser->id !== (int) $userId) {
            // Relation: following() belongsToMany
            $currentUser->following()->toggle($userId);
        }
    }

    // Toggle Like Logic
    public function toggleLike($postId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->redirectRoute('login');
        }

        $post = Post::findOrFail($postId);

        if (method_exists($post, 'likes')) {
            if ($post->likes()->where('user_id', $user->id)->exists()) {
                $post->likes()->where('user_id', $user->id)->delete();
            } else {
                $post->likes()->create(['user_id' => $user->id]);
            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
       $currentUser = auth()->user();

        $storyUsers = User::with('profile')
            ->whereHas('followers', function ($query) use ($currentUser) {
                $query->where('follower_id', $currentUser->id);
            })
            
            ->where('status', true)
            ->withoutRole(['Admin', 'Super Admin']) 
            ->take(10)
            ->get();

        $posts = Post::with([
                'user.profile',
                'product',
                'likes',
                'product.images'
            ])
            ->whereHas('user', function ($query) {
                $query->withoutRole(['Admin', 'Super Admin']);
            })
            ->latest()
            ->paginate(10);
        
        //dd($posts);

        return view('livewire.home-feed', [
            'storyUsers' => $storyUsers,
            'posts' => $posts,
        ]);
    }
}