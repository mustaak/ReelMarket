<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{
    public function toggle(Request $request, User $user, FollowService $followService): RedirectResponse
    {
        $follower = $request->user();
        abort_unless($follower instanceof User, Response::HTTP_FORBIDDEN);

        $followService->toggle($follower, $user);

        return redirect()->route('users.show', $user);
    }

    public function accept(Request $request, Follow $follow, FollowService $followService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, Response::HTTP_FORBIDDEN);

        $followService->accept($follow, $actor);

        return back();
    }

    public function reject(Request $request, Follow $follow, FollowService $followService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, Response::HTTP_FORBIDDEN);

        $followService->reject($follow, $actor);

        return back();
    }
}
