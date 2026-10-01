<?php

namespace App\Services;

use App\Models\Follow;
use App\Models\User;
use App\Notifications\SocialActivityNotification;

class FollowService
{
    public function toggle(User $follower, User $following): ?Follow
    {
        abort_if($follower->is($following), 403);

        $followingProfile = $following->profile()->firstOrCreate([]);

        $existingFollow = Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $following->id)
            ->first();

        

        if ($existingFollow) {
            if ($existingFollow->status === 'pending' && $followingProfile->is_public) {
                $existingFollow->update(['status' => 'accepted']);
                $follower->notify(new SocialActivityNotification(
                    $following,
                    'follow_accepted',
                    'your follow request was accepted because this account is now public.',
                    route('users.show', $following),
                ));

                return $existingFollow;
            }

            $existingFollow->delete();

            return null;
        }

        $follower->profile()->firstOrCreate([]);
        $follow = Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
            'status' => $followingProfile->is_public ? 'accepted' : 'pending',
        ]);

        //dd($follow);

        $isRequest = $follow->status === 'pending';
        $following->notify(new SocialActivityNotification(
            $follower,
            $isRequest ? 'follow_request' : 'new_follower',
            $isRequest ? 'sent you a follow request.' : 'started following you.',
            route('users.show', $follower),
        ));

        return $follow;
    }

    public function acceptPendingRequestsForPublicProfile(User $user): void
    {
        $profile = $user->profile()->firstOrCreate([]);

        if (! $profile->is_public) {
            return;
        }

        $pendingRequests = $user->receivedFollows()
            ->where('status', 'pending')
            ->with('follower')
            ->get();

        foreach ($pendingRequests as $follow) {
            $follow->update(['status' => 'accepted']);
            $follow->follower->notify(new SocialActivityNotification(
                $user,
                'follow_accepted',
                'your follow request was accepted because this account is now public.',
                route('users.show', $user),
            ));
        }
    }

    public function accept(Follow $follow, User $actor): void
    {
        abort_unless($follow->following_id === $actor->id, 403);
        abort_unless($follow->status === 'pending', 404);

        $follow->update(['status' => 'accepted']);
        $follow->follower->notify(new SocialActivityNotification(
            $actor,
            'follow_accepted',
            'accepted your follow request.',
            route('users.show', $actor),
        ));
    }

    public function reject(Follow $follow, User $actor): void
    {
        abort_unless($follow->following_id === $actor->id, 403);
        abort_unless($follow->status === 'pending', 404);

        $follow->delete();
    }
}
