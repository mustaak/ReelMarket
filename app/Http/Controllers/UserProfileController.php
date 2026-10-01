<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function show(User $user): View
    {
        // dd("here");

        $viewer = Auth::user();
        $isOwner = $viewer instanceof User && $viewer->is($user);
        $existingFollow = $viewer instanceof User
            ? Follow::query()
                ->where('follower_id', $viewer->id)
                ->where('following_id', $user->id)
                ->first()
            : null;

        $reverseFollow = $viewer instanceof User
        ? Follow::query()
            ->where('follower_id', $user->id)
            ->where('following_id', $viewer->id)
            ->where('status', 'accepted')
            ->exists()
        : false;

        $profile = $user->profile;
        $isPublic = (bool) ($profile?->is_public ?? true);

        $postsQuery = $user->posts()
            ->visibleTo($viewer instanceof User ? $viewer : null)
            ->with(['images', 'product.images'])
            ->latest();
        $reelsQuery = $user->reels()
            ->visibleTo($viewer instanceof User ? $viewer : null)
            ->with('product')
            ->latest();
        $profileTab = request()->query('tab');

        if (! in_array($profileTab, ['posts', 'reels', 'followers', 'following'], true)) {
            $profileTab = 'posts';
        }

        $posts = $profileTab === 'posts' ? $postsQuery->paginate(12) : collect();
        $reels = $profileTab === 'reels' ? $reelsQuery->paginate(12) : collect();

        $canViewConnections = $isOwner
            || $isPublic
            || ($viewer instanceof User && $viewer->isFollowing($user));

        $connections = collect();

        if ($canViewConnections) {
            $connections = match ($profileTab) {
                'followers' => $user->acceptedFollowers()->with('profile')->orderBy('users.name')->paginate(30),
                'following' => $isOwner
                    ? $user->following()
                        ->wherePivotIn('status', ['accepted', 'pending'])
                        ->with('profile')
                        ->orderBy('users.name')
                        ->paginate(30)
                    : $user->acceptedFollowing()->with('profile')->orderBy('users.name')->paginate(30),
                default => collect(),
            };
        }

        //dd($existingFollow?->status);

        return view('profile', [
            'user' => $user,
            'profile' => $profile ?? $user->profile()->make(['is_public' => true]),
            'posts' => $posts,
            'reels' => $reels,
            'connections' => $connections,
            'profileTab' => $profileTab,
            'postsCount' => $user->posts()->visibleTo($viewer instanceof User ? $viewer : null)->count(),
            'reelsCount' => $user->reels()->visibleTo($viewer instanceof User ? $viewer : null)->count(),
            'followersCount' => $user->acceptedFollowers()->count(),
            'followingCount' => $isOwner
                ? $user->sentFollows()->whereIn('status', ['accepted', 'pending'])->count()
                : $user->acceptedFollowing()->count(),
            'isOwnProfile' => $isOwner,
            'isPrivateProfile' => ! $isPublic,
            'followStatus' => $existingFollow?->status,
            'reverseFollowAccepted' => $reverseFollow,
            'pendingFollowRequests' => $isOwner
                ? Follow::query()
                    ->where('following_id', $user->id)
                    ->where('status', 'pending')
                    ->with('follower.profile')
                    ->latest()
                    ->get()
                : collect(),
        ]);
    }
}
