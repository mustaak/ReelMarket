<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['status'] = true;

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        throw ValidationException::withMessages([
            'email' => ['These credentials do not match our records.'],
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => true,
            'type' => 'user',
        ]);

        if (class_exists('\Spatie\\Permission\\Models\\Role')) {
            $user->assignRole('User');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function profile()
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $profile = $user->profile()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'bio' => 'Style enthusiast and creator.',
            'is_public' => true,
        ]);

        $profileTab = request()->query('tab');

        if (! in_array($profileTab, ['posts', 'reels', 'followers', 'following'], true)) {
            $profileTab = 'posts';
        }
        $postsQuery = $user->posts()
            ->visibleTo($user)
            ->with(['images', 'product.images'])
            ->latest();
        $reelsQuery = $user->reels()
            ->visibleTo($user)
            ->with('product')
            ->latest();
        $posts = $profileTab === 'posts' ? $postsQuery->paginate(12) : collect();
        $reels = $profileTab === 'reels' ? $reelsQuery->paginate(12) : collect();

        $connections = match ($profileTab) {
            'followers' => $user->acceptedFollowers()->with('profile')->orderBy('users.name')->paginate(30),
            'following' => $user->following()
                ->wherePivotIn('status', ['accepted', 'pending'])
                ->with('profile')
                ->orderBy('users.name')
                ->paginate(30),
            default => collect(),
        };

        $postsCount = $user->posts()->visibleTo($user)->count();

        $reelsCount = $user->reels()->visibleTo($user)->count();

        $followersCount = $user->acceptedFollowers()->count();

        

        $followingCount = $user->sentFollows()
            ->whereIn('status', ['accepted', 'pending'])
            ->count();

        $pendingFollowRequests = Follow::query()
            ->where('following_id', $user->id)
            ->where('status', 'pending')
            ->with('follower.profile')
            ->latest()
            ->get();

        // dd($posts);

        return view('profile', [
            'user' => $user,
            'profile' => $profile,
            'posts' => $posts,
            'reels' => $reels,
            'connections' => $connections,
            'profileTab' => $profileTab,
            'postsCount' => $postsCount,
            'reelsCount' => $reelsCount,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'isOwnProfile' => true,
            'isPrivateProfile' => ! (bool) $profile->is_public,
            'followStatus' => null,
            'pendingFollowRequests' => $pendingFollowRequests,
        ]);
    }

    public function updateProfilePrivacy(Request $request, FollowService $followService): RedirectResponse
    {
        $validated = $request->validate([
            'is_private' => ['sometimes', 'boolean'],
        ]);

        $user = Auth::user();

        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        $profile = $user->profile()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'is_public' => true,
        ]);

        $isPrivate = (bool) ($validated['is_private'] ?? false);
        $profile->update(['is_public' => ! $isPrivate]);

        if (! $isPrivate) {
            $followService->acceptPendingRequestsForPublicProfile($user);
        }

        return redirect()->route('profile')->with(
            'success',
            $isPrivate ? 'Your account is now private.' : 'Your account is now public.',
        );
    }

    public function createPost()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return view('create-post');
    }

    public function storePost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'] ?? null,
            'status' => 'published',
            'visibility' => 'public',
            'image' => null,
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $post->image = $path;
            $post->save();
        }

        return redirect()->route('profile')->with('success', 'Your post is live.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
