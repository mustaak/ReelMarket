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
        //dd("here");

        $viewer = Auth::user();
        $isOwner = $viewer instanceof User && $viewer->is($user);
        $existingFollow = $viewer instanceof User
            ? Follow::query()
                ->where('follower_id', $viewer->id)
                ->where('following_id', $user->id)
                ->first()
            : null;
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
        $profileTab = request()->query('tab') === 'reels' ? 'reels' : 'posts';
        $posts = $profileTab === 'posts' ? $postsQuery->paginate(12) : collect();
        $reels = $profileTab === 'reels' ? $reelsQuery->paginate(12) : collect();

        

        return view('profile', [
            'user' => $user,
            'profile' => $profile ?? $user->profile()->make(['is_public' => true]),
            'posts' => $posts,
            'reels' => $reels,
            'profileTab' => $profileTab,
            'postsCount' => $user->posts()->visibleTo($viewer instanceof User ? $viewer : null)->count(),
            'reelsCount' => $user->reels()->visibleTo($viewer instanceof User ? $viewer : null)->count(),
            'followersCount' => $user->followers()->wherePivot('status', 'accepted')->count(),
            'followingCount' => $user->following()->wherePivot('status', 'accepted')->count(),
            'isOwnProfile' => $isOwner,
            'isPrivateProfile' => ! $isPublic,
            'followStatus' => $existingFollow?->status,
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
