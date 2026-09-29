<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} | Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0b0813] text-slate-100">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-800 bg-[#140e26] shadow-[0_10px_30px_rgba(0,0,0,0.28)]">
            <div class="relative h-48 overflow-hidden rounded-t-3xl bg-gradient-to-r from-[#f59e0b] via-[#a855f7] to-[#60a5fa]">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.35),transparent_40%)]"></div>
                <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-[#140e26] to-transparent"></div>
            </div>

            <div class="px-5 pb-8 sm:px-7">
                <div class="relative -mt-14 flex items-end justify-between gap-4">
                    <div class="flex items-end gap-4">
                        <div class="flex h-28 w-28 items-center justify-center rounded-full border-4 border-[#140e26] bg-gradient-to-br from-[#f9a8d4] via-[#c084fc] to-[#60a5fa] text-3xl font-black text-white shadow-xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="pb-3">
                            <h1 class="text-2xl font-black text-white">{{ $user->name }}</h1>
                            <p class="text-sm text-slate-400">{{ ucfirst($user->type ?? 'user') }}</p>
                        </div>
                    </div>

                    <div class="hidden items-center gap-2 sm:flex">
                        <a href="{{ route('home') }}" class="rounded-xl border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 hover:border-slate-500 hover:text-white">
                            Home
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-[#f59e0b] px-4 py-2 text-sm font-semibold text-black transition hover:brightness-110">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3 rounded-2xl border border-slate-800 bg-[#1a1333] p-4 text-center shadow-inner">
                    <div>
                        <p class="text-xl font-black text-white">{{ $postsCount }}</p>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Posts</p>
                    </div>
                    <div>
                        <p class="text-xl font-black text-white">{{ $followersCount }}</p>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Followers</p>
                    </div>
                    <div>
                        <p class="text-xl font-black text-white">{{ $followingCount }}</p>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Following</p>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-slate-800 bg-[#1a1333] p-5">
                    <p class="text-sm leading-7 text-slate-300">
                        {{ $profile->bio ?? 'Create your story, share your look, and discover products you love.' }}
                    </p>
                </div>

                <div class="mt-8 flex items-center justify-between">
                    <h2 class="text-lg font-black text-white">Recent posts</h2>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('posts.create') }}" class="rounded-xl bg-[#f59e0b] px-3 py-2 text-sm font-semibold text-black transition hover:brightness-110">
                            Create Post
                        </a>
                        <a href="{{ route('home') }}" class="text-sm font-medium text-amber-400 hover:text-amber-300">View feed</a>
                    </div>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($user->posts()->latest()->take(3)->get() as $post)
                        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-[#1a1333]">
                            @php
                                $image = $post->images->first()?->image_path ?? $post->image ?? null;
                            @endphp
                            @if ($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="Post image" class="h-52 w-full object-cover">
                            @else
                                <div class="flex h-52 items-center justify-center bg-gradient-to-br from-[#f59e0b]/20 via-[#a855f7]/20 to-[#60a5fa]/20 text-lg font-bold text-slate-200">
                                    Post
                                </div>
                            @endif
                            <div class="p-4">
                                <p class="line-clamp-3 text-sm text-slate-300">{{ $post->content ?: 'New post from ' . $user->name }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-700 bg-[#1a1333] p-8 text-center text-sm text-slate-400 sm:col-span-2 xl:col-span-3">
                            No posts yet. Share your first post from the main feed.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>
