<?php

namespace App\Livewire;

use App\Models\Follow;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class FollowSuggestions extends Component
{
    public function toggleFollow(int $userId, FollowService $followService): void
    {
        $follower = auth()->user();

        if (! $follower instanceof User) {
            $this->redirectRoute('login');

            return;
        }

        $following = User::query()
            ->where('status', true)
            ->withoutRole(['Admin', 'Super Admin'])
            ->findOrFail($userId);

        $followService->toggle($follower, $following);
    }

    public function render()
    {
        $viewer = auth()->user();

        $suggestions = User::query()
            ->with([
                'profile',
                'activeStories',
            ])
            ->withCount([
                'followers as accepted_followers_count' => fn (Builder $query) => $query
                    ->where('follows.status', 'accepted'),
            ])
            ->where('status', true)
            ->withoutRole(['Admin', 'Super Admin'])
            ->when($viewer instanceof User, function (Builder $query) use ($viewer): void {
                $query->whereKeyNot($viewer->id)
                    ->whereDoesntHave('followers', fn (Builder $followers) => $followers
                        ->where('follows.follower_id', $viewer->id)
                        ->whereIn('follows.status', ['accepted', 'pending']));
            })
            ->orderByDesc('accepted_followers_count')
            ->orderByDesc('id')
            ->limit(10)
            ->get();


        $followStatuses = $viewer instanceof User
            ? Follow::query()
                ->where('follower_id', $viewer->id)
                ->whereIn('following_id', $suggestions->modelKeys())
                ->pluck('status', 'following_id')
            : collect();

        $reverseFollowStatuses = $viewer instanceof User
        ? Follow::query()
            ->whereIn('follower_id', $suggestions->modelKeys())
            ->where('following_id', $viewer->id)
            ->where('status', 'accepted')
            ->pluck('status', 'follower_id')
            ->map(fn () => true)
        : collect();

        return view('livewire.follow-suggestions', [
            'suggestions' => $suggestions,
            'followStatuses' => $followStatuses,
            'reverseFollowStatuses' => $reverseFollowStatuses,
        ]);
    }
}
