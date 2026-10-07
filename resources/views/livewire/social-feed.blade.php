<div class="w-full">

    {{-- ============================================================
        GRID: LEFT (FEED 8) + RIGHT (SIDEBAR 4)
    ============================================================ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        {{-- ========================================================
            LEFT COLUMN — STORIES + TABS + FEED
        ========================================================= --}}
        <div class="space-y-5 lg:col-span-8">

            {{-- ---------- STORIES ROW ---------- --}}
            <section class="theme-card rounded-2xl border border-slate-800/80 p-4 shadow-xl">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-300">Stories</h3>
                    <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                        {{ $storyUsers->count() }} creators
                    </span>
                </div>

                <div class="no-scrollbar flex gap-3 overflow-x-auto pb-1">

                    {{-- Add story --}}
                    <div class="shrink-0">
                        <livewire:story.create-story />
                    </div>

                    {{-- Followed creators --}}
                    @foreach ($storyUsers as $sUser)
                        @php
                            $activeStory = $sUser->activeStories->first();
                            $hasStory = $activeStory !== null;
                            $hasViewed = $hasStory ? $activeStory->views->contains('user_id', auth()->id()) : false;
                        @endphp

                        <button type="button" wire:key="story-user-{{ $sUser->id }}"
                            @if ($hasStory) wire:click="$dispatch('open-story', { storyId: {{ $activeStory->id }} })" @endif
                            class="flex min-w-[72px] shrink-0 flex-col items-center gap-2 text-center">

                            <div @class([
                                'relative size-16 rounded-full p-[3px]',
                                'bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600' =>
                                    $hasStory && !$hasViewed,
                                'bg-slate-600' => $hasStory && $hasViewed,
                                'bg-slate-800' => !$hasStory,
                            ])>
                                <div
                                    class="h-full w-full overflow-hidden rounded-full border-2 border-slate-950 bg-slate-950">
                                    @if (optional($sUser->profile)->profile_picture)
                                        <img src="{{ asset('storage/' . $sUser->profile->profile_picture) }}"
                                            alt="{{ $sUser->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span
                                            class="flex h-full w-full items-center justify-center text-sm font-black uppercase text-white">
                                            {{ Str::substr($sUser->name, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <span class="max-w-[70px] truncate text-[11px] font-semibold text-slate-300">
                                {{ Str::before($sUser->name, ' ') }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </section>

            {{-- ---------- MOBILE: SUGGESTED USERS (horizontal) ---------- --}}
            <div class="theme-card rounded-2xl border border-slate-800/80 p-4 shadow-xl lg:hidden">
                <livewire:follow-suggestions :limit="6" variant="scroll" />
            </div>

            {{-- ---------- FEED TABS ---------- --}}
            <div class="theme-card rounded-2xl border border-slate-800/80 p-1.5 shadow-xl">
                <nav class="no-scrollbar flex gap-1 overflow-x-auto">
                    @php
                        $tabs = [
                            'for_you' => 'For You',
                            'following' => 'Following',
                            'reels' => 'Reels',
                            'shop' => 'Shop Posts',
                        ];
                    @endphp

                    @foreach ($tabs as $key => $label)
                        <button type="button" wire:click="setTab('{{ $key }}')"
                            wire:key="tab-{{ $key }}" @class([
                                'shrink-0 rounded-xl px-4 py-2 text-xs font-black uppercase tracking-wide transition',
                                'theme-btn shadow-sm' => $activeTab === $key,
                                'text-slate-400 hover:bg-white/5 hover:text-white' => $activeTab !== $key,
                            ])>
                            {{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- ---------- POSTS FEED ---------- --}}
            @if ($activeTab === 'reels')
                {{-- Reels grid --}}
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    @forelse ($reels as $reel)
                        <a href="{{ route('reels.index') }}" wire:key="social-reel-{{ $reel->id }}"
                            class="group theme-card relative block aspect-[3/4] overflow-hidden rounded-2xl border border-slate-800/80">
                            @if ($reel->thumbnail)
                                <img src="{{ asset('storage/' . $reel->thumbnail) }}" alt="Reel"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-600">
                                    <x-heroicon-o-video-camera class="size-10" />
                                </div>
                            @endif

                            <span class="absolute inset-0 flex items-center justify-center">
                                <x-heroicon-s-play-circle class="size-11 text-white/85" />
                            </span>

                            <span
                                class="absolute inset-x-0 bottom-0 space-y-0.5 bg-gradient-to-t from-black/80 to-transparent px-3 py-2">
                                <span class="block truncate text-[11px] font-bold text-white">
                                    {{ $reel->user?->profile?->username ?? $reel->user?->name }}
                                </span>
                                @if ($reel->product)
                                    <span class="theme-text block truncate text-[10px] font-semibold">
                                        {{ $reel->product->name }}
                                    </span>
                                @endif
                            </span>
                        </a>
                    @empty
                        <div class="theme-card col-span-full rounded-2xl border border-slate-800/80 p-10 text-center">
                            <p class="text-3xl">🎬</p>
                            <h3 class="mt-4 text-lg font-black text-white">No reels yet</h3>
                            <p class="mt-2 text-sm text-slate-400">Check back soon.</p>
                        </div>
                    @endforelse
                </div>
            @else
                {{-- Posts feed --}}
                <div class="space-y-5">
                    @forelse ($posts as $post)
                        @php
                            $author = $post->user;
                            $authorProfile = $author?->profile;
                            $followStatus = $followStatuses[$post->user_id] ?? null;
                            $isFollowing = $followStatus === 'accepted';
                            $isFollowRequested = $followStatus === 'pending';
                            $isLiked = auth()->check() && in_array($post->id, $likedPostIds, true);
                            $isBookmarked = auth()->check() && in_array($post->id, $bookmarkedPostIds, true);
                            $postImage = $post->image ?: $post->images?->first()?->image_path ?? null;
                            $productImage = $post->product?->images?->first()?->image;
                        @endphp

                        <article id="post-{{ $post->id }}" wire:key="social-post-{{ $post->id }}"
                            class="theme-card overflow-hidden rounded-2xl border border-slate-800/80 shadow-xl">

                            {{-- Header: avatar + name + follow --}}
                            <div class="flex items-center justify-between gap-3 border-b border-slate-800/60 p-4">
                                <div class="flex min-w-0 items-center gap-3">

                                    @if ($author?->activeStories?->isNotEmpty())
                                        <button type="button"
                                            wire:click="$dispatch('open-story', { storyId: {{ $author->activeStories->first()->id }} })"
                                            class="theme-btn relative flex size-11 shrink-0 items-center justify-center rounded-full p-[2px]">
                                            <div
                                                class="flex size-full items-center justify-center overflow-hidden rounded-full bg-slate-950 text-sm font-black uppercase">
                                                @if ($authorProfile?->profile_picture)
                                                    <img src="{{ asset('storage/' . $authorProfile->profile_picture) }}"
                                                        alt="{{ $author?->name }}"
                                                        class="h-full w-full object-cover" />
                                                @else
                                                    {{ Str::substr($author?->name ?? 'U', 0, 2) }}
                                                @endif
                                            </div>
                                        </button>
                                    @else
                                        <a href="{{ route('users.show', $author) }}"
                                            class="theme-btn flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-full text-sm font-black uppercase">
                                            @if ($authorProfile?->profile_picture)
                                                <img src="{{ asset('storage/' . $authorProfile->profile_picture) }}"
                                                    alt="{{ $author?->name }}" class="h-full w-full object-cover" />
                                            @else
                                                {{ Str::substr($author?->name ?? 'U', 0, 2) }}
                                            @endif
                                        </a>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('users.show', $author) }}"
                                                class="truncate text-sm font-bold text-white">
                                                {{ $authorProfile?->username ?? $author?->name }}
                                            </a>
                                            @if ($authorProfile?->is_verified)
                                                <x-heroicon-s-check-circle class="theme-text size-4" />
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-400">
                                            {{ $post->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                @if (auth()->check() && auth()->id() !== $post->user_id)
                                    <button wire:click="toggleFollow({{ $post->user_id }})" type="button"
                                        class="rounded-full border px-3 py-1.5 text-[11px] font-black uppercase tracking-wide transition {{ $isFollowing || $isFollowRequested ? 'border-slate-700 bg-slate-900 text-slate-200' : 'theme-btn text-black' }}">
                                        {{ $isFollowing ? 'Following' : ($isFollowRequested ? 'Requested' : 'Follow') }}
                                    </button>
                                @endif
                            </div>

                            {{-- Media --}}
                            <div class="relative aspect-[4/5] w-full overflow-hidden bg-slate-900">
                                @if ($postImage)
                                    <img src="{{ asset('storage/' . $postImage) }}" alt="Post content"
                                        class="h-full w-full object-cover" />
                                @else
                                    <div
                                        class="flex h-full w-full items-center justify-center text-sm font-bold uppercase tracking-[0.25em] text-slate-500">
                                        Media
                                    </div>
                                @endif
                            </div>

                            {{-- Actions + caption + product --}}
                            <div class="space-y-4 p-4">

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <button wire:click="toggleLike({{ $post->id }})" type="button"
                                            class="flex items-center gap-2 text-slate-200 transition hover:text-white">
                                            @if ($isLiked)
                                                <x-heroicon-s-heart class="size-6 text-rose-500" />
                                            @else
                                                <x-heroicon-o-heart class="size-6" />
                                            @endif
                                            <span class="text-xs font-bold">
                                                {{ number_format(max((int) ($post->likes_count ?? 0), $post->likes->count())) }}
                                            </span>
                                        </button>

                                        <button type="button" wire:click="openComments({{ $post->id }})"
                                            class="flex items-center gap-2 text-slate-200 transition hover:text-white">
                                            <x-heroicon-o-chat-bubble-left class="size-5" />
                                            <span class="text-xs font-bold">
                                                {{ max((int) ($post->comments_count ?? 0), $post->comments->count()) }}
                                            </span>
                                        </button>

                                        <button type="button"
                                            class="flex items-center gap-2 text-slate-200 transition hover:text-white">
                                            <x-heroicon-o-paper-airplane class="size-5" />
                                        </button>
                                    </div>

                                    <button type="button" wire:click="toggleBookmark({{ $post->id }})"
                                        aria-label="{{ $isBookmarked ? 'Remove saved post' : 'Save post' }}"
                                        class="transition hover:text-white {{ $isBookmarked ? 'theme-text' : 'text-slate-300' }}">
                                        @if ($isBookmarked)
                                            <x-heroicon-s-bookmark class="size-5" />
                                        @else
                                            <x-heroicon-o-bookmark class="size-5" />
                                        @endif
                                    </button>
                                </div>

                                @if ($post->content)
                                    <p class="text-sm leading-6 text-slate-300">
                                        <span class="mr-2 font-black text-white">
                                            {{ $authorProfile?->username ?? $author?->name }}
                                        </span>
                                        {{ $post->content }}
                                    </p>
                                @endif

                                @if ($post->product)
                                    <div
                                        class="theme-inner flex items-center justify-between gap-3 rounded-2xl border border-slate-800/80 p-3">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div
                                                class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-900">
                                                @if ($productImage)
                                                    <img src="{{ asset('storage/' . $productImage) }}"
                                                        alt="{{ $post->product->name }}"
                                                        class="h-full w-full object-cover" />
                                                @else
                                                    <span class="text-[10px] font-black uppercase text-slate-400">
                                                        {{ Str::substr($post->product->name, 0, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-xs font-bold text-white">
                                                    {{ $post->product->name }}
                                                </p>
                                                <p class="theme-text text-[11px] font-bold">
                                                    ₹{{ number_format((float) ($post->product->sale_price ?? $post->product->price), 0) }}
                                                </p>
                                            </div>
                                        </div>

                                        <a href="{{ route('product.detail', $post->product->slug ?? $post->product->id) }}"
                                            class="theme-btn shrink-0 rounded-xl px-3.5 py-2 text-[11px] font-black uppercase tracking-wide">
                                            Shop now
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="theme-card rounded-2xl border border-slate-800/80 p-10 text-center shadow-xl">
                            <p class="text-3xl">📸</p>
                            <h3 class="mt-4 text-lg font-black text-white">No posts yet</h3>
                            <p class="mt-2 text-sm text-slate-400">
                                Follow creators to see their posts here.
                            </p>
                        </div>
                    @endforelse

                    @if ($posts->hasPages())
                        <div class="pb-6">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- ========================================================
            RIGHT COLUMN — STICKY SIDEBAR
        ========================================================= --}}
        <aside class="hidden lg:col-span-4 lg:block">
            <div class="sticky top-36 space-y-5">

                {{-- ---------- SUGGESTED FOR YOU ---------- --}}
                <livewire:follow-suggestions :limit="5" variant="list" />

                {{-- ---------- TRENDING REELS ---------- --}}
                @if ($trendingReels->isNotEmpty())
                    <div class="theme-card rounded-2xl border border-slate-800/80 p-4 shadow-xl">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase tracking-[0.18em] text-slate-300">
                                Trending reels
                            </h3>
                            <a href="{{ route('reels.index') }}"
                                class="theme-text text-[10px] font-bold uppercase tracking-wider hover:underline">
                                Watch all
                            </a>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($trendingReels as $reel)
                                <a href="{{ route('reels.index') }}" wire:key="side-reel-{{ $reel->id }}"
                                    class="group relative block aspect-[3/4] overflow-hidden rounded-xl border border-slate-800/80">
                                    @if ($reel->thumbnail)
                                        <img src="{{ asset('storage/' . $reel->thumbnail) }}" alt="Reel"
                                            class="h-full w-full object-cover transition group-hover:scale-105">
                                    @else
                                        <div
                                            class="flex h-full w-full items-center justify-center bg-slate-900 text-slate-600">
                                            <x-heroicon-o-video-camera class="size-6" />
                                        </div>
                                    @endif

                                    <span class="absolute inset-0 flex items-center justify-center">
                                        <x-heroicon-s-play-circle class="size-6 text-white/85" />
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ---------- SHOP BY CATEGORY ---------- --}}
                @if ($categories->isNotEmpty())
                    <div class="theme-card rounded-2xl border border-slate-800/80 p-4 shadow-xl">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase tracking-[0.18em] text-slate-300">
                                Shop by category
                            </h3>
                            <a href="{{ route('shop.index') }}"
                                class="theme-text text-[10px] font-bold uppercase tracking-wider hover:underline">
                                View all
                            </a>
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($categories as $cat)
                                <a href="{{ route('shop.index', ['selectedCategory' => $cat->slug ?? $cat->id]) }}"
                                    wire:key="side-cat-{{ $cat->id }}"
                                    class="group flex flex-col items-center gap-1.5 text-center">

                                    <span
                                        class="theme-inner flex size-12 items-center justify-center overflow-hidden rounded-xl border border-slate-800 transition group-hover:border-(--accent-primary)">
                                        @if ($cat->image)
                                            <img src="{{ asset('storage/' . $cat->image) }}"
                                                alt="{{ $cat->name }}" class="h-full w-full object-cover">
                                        @else
                                            <x-heroicon-o-tag class="size-5 text-slate-400" />
                                        @endif
                                    </span>

                                    <span
                                        class="w-full truncate text-[10px] font-semibold text-slate-400 group-hover:text-white">
                                        {{ $cat->name }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ---------- FOOTER LINKS ---------- --}}
                <p class="px-2 text-[11px] leading-relaxed text-slate-500">
                    About · Help · Privacy · Terms · Creators · Ads · API · Jobs
                    <br>
                    <span class="mt-1 block">© {{ date('Y') }} YourBrand</span>
                </p>
            </div>
        </aside>
    </div>

    {{-- ============================================================
        COMMENTS DRAWER
    ============================================================ --}}
    @if ($activeCommentsPostId)
        @php $activePost = $posts->firstWhere('id', $activeCommentsPostId); @endphp
        <div class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Comments"
            wire:keydown.escape.window="closeComments">
            <div class="absolute inset-0 bg-black/60" wire:click="closeComments"></div>

            <div
                class="theme-card absolute inset-x-0 bottom-0 flex max-h-[70vh] flex-col rounded-t-2xl border-t border-slate-800/80 pb-[env(safe-area-inset-bottom)] md:inset-x-auto md:bottom-6 md:right-6 md:w-96 md:rounded-2xl md:border md:pb-0">
                <header class="flex items-center justify-between border-b border-slate-800/80 px-4 py-3">
                    <h2 class="text-sm font-black text-white">Comments</h2>
                    <button type="button" wire:click="closeComments" aria-label="Close"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-white/5 hover:text-white">
                        <x-heroicon-o-x-mark class="size-5" />
                    </button>
                </header>

                <ul class="flex-1 space-y-3 overflow-y-auto px-4 py-3">
                    @forelse ($activePost?->comments ?? [] as $comment)
                        <li class="flex items-start gap-2.5">
                            <span
                                class="theme-soft-bg flex size-7 shrink-0 items-center justify-center rounded-full text-[11px] font-black text-white">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </span>
                            <p class="text-sm text-slate-200">
                                <span class="font-bold text-white">{{ $comment->user->name }}</span>
                                <span class="ml-1 text-slate-300">{{ $comment->content }}</span>
                            </p>
                        </li>
                    @empty
                        <li class="py-10 text-center text-xs text-slate-500">
                            No comments yet. Be the first.
                        </li>
                    @endforelse
                </ul>

                <form wire:submit="postComment" class="flex items-center gap-2 border-t border-slate-800/80 p-3">
                    <input wire:model="newComment" type="text" placeholder="Write a comment..."
                        class="theme-inner min-w-0 flex-1 rounded-full border border-slate-700 px-4 py-2 text-sm text-white placeholder:text-slate-500 focus:border-(--accent-primary) focus:outline-none">
                    <button type="submit" aria-label="Post comment"
                        class="theme-btn flex size-10 shrink-0 items-center justify-center rounded-full">
                        <x-heroicon-o-paper-airplane class="size-4" />
                    </button>
                </form>
                @error('newComment')
                    <p class="px-3 pb-2 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @endif

    {{-- Global components --}}
    <livewire:report-content />
    <livewire:story.story-viewer />
</div>
