<?php

namespace App\Livewire;

use App\Models\Bookmark;
use App\Models\Post;
use App\Models\Reel;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SavedContentPage extends Component
{
    public function removePostBookmark(int $postId): void
    {
        $this->currentUser()->bookmarks()
            ->where('bookmarkable_type', Post::class)
            ->where('bookmarkable_id', $postId)
            ->delete();
    }

    public function removeReelBookmark(int $reelId): void
    {
        $this->currentUser()->bookmarks()
            ->where('bookmarkable_type', Reel::class)
            ->where('bookmarkable_id', $reelId)
            ->delete();
    }

    public function render()
    {
        $user = $this->currentUser();
        $postIds = Bookmark::query()
            ->where('user_id', $user->id)
            ->where('bookmarkable_type', Post::class)
            ->pluck('bookmarkable_id');
        $reelIds = Bookmark::query()
            ->where('user_id', $user->id)
            ->where('bookmarkable_type', Reel::class)
            ->pluck('bookmarkable_id');

        return view('livewire.saved-content-page', [
            'posts' => Post::visibleTo($user)
                ->whereIn('id', $postIds)
                ->with(['user.profile', 'images', 'product'])
                ->latest()
                ->get(),
            'reels' => Reel::visibleTo($user)
                ->whereIn('id', $reelIds)
                ->with('user.profile')
                ->latest()
                ->get(),
        ]);
    }

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
