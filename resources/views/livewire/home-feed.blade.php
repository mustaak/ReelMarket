<div class="mx-auto w-full max-w-2xl space-y-5 md:space-y-6">
    <section class="flex items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.22em] theme-text">YourBrand</p>
            <h1 class="mt-1 text-2xl font-black text-white">Your feed</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('shop.index') }}" class="rounded-lg border border-slate-700 px-3 py-2 text-xs font-bold text-slate-200 transition hover:border-slate-500 hover:text-white">
                Shop
            </a>
            @auth
                <a href="{{ route('saved.index') }}" class="rounded-lg border border-slate-700 px-3 py-2 text-xs font-bold text-slate-200 transition hover:border-slate-500 hover:text-white">Saved</a>
                <a href="{{ route('posts.create') }}" class="theme-btn inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-black">
                    <x-heroicon-o-plus class="size-4" />
                    <span>Create post</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="theme-btn rounded-lg px-3 py-2 text-xs font-black">
                    Join
                </a>
            @endauth
        </div>
    </section>

    @if(isset($storyUsers) && $storyUsers->isNotEmpty())
        <section class="theme-card rounded-lg border border-slate-800/80 p-4 shadow-xl sm:p-5">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-300">Following</h2>
                <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $storyUsers->count() }} creators</span>
            </div>

            <div class="flex gap-3 overflow-x-auto pb-1 no-scrollbar">
                @foreach($storyUsers as $sUser)
                    <a href="{{ route('users.show', $sUser) }}" wire:key="story-user-{{ $sUser->id }}" class="flex min-w-[72px] shrink-0 cursor-pointer flex-col items-center gap-2 text-center">
                        <div class="relative size-16 rounded-full p-[2px] theme-btn shadow-lg shadow-black/30">
                            <div class="flex h-full w-full items-center justify-center overflow-hidden rounded-full bg-slate-950">
                                @if(optional($sUser->profile)->profile_picture)
                                    <img src="{{ asset('storage/' . $sUser->profile->profile_picture) }}" alt="{{ $sUser->name }}" class="h-full w-full object-cover" />
                                @else
                                    <span class="text-sm font-black uppercase text-black">
                                        {{ Str::substr($sUser->name, 0, 1) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="max-w-[70px] truncate text-[11px] font-semibold text-slate-300">
                            {{ Str::before($sUser->name, ' ') }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @forelse($posts as $post)
        @php
            $author = $post->user;
            $authorProfile = $author?->profile;
            $followStatus = $followStatuses[$post->user_id] ?? null;
            $isFollowing = $followStatus === 'accepted';
            $isFollowRequested = $followStatus === 'pending';
            $isLiked = auth()->check() && in_array($post->id, $likedPostIds, true);
            $isBookmarked = auth()->check() && in_array($post->id, $bookmarkedPostIds, true);
            $postImage = $post->image ?: ($post->images?->first()?->image_path ?? null);
            $productImage = $post->product?->images?->first()?->image;
        @endphp

        <article id="post-{{ $post->id }}" wire:key="post-card-{{ $post->id }}" class="theme-card overflow-hidden rounded-lg border border-slate-800/80 shadow-xl">
            <div class="flex items-center justify-between gap-3 border-b border-slate-800/60 p-4">
                <a href="{{ route('users.show', $author) }}" class="flex min-w-0 items-center gap-3">
                    <div class="flex size-11 items-center justify-center overflow-hidden rounded-full theme-btn text-sm font-black uppercase">
                        @if($authorProfile?->profile_picture)
                            <img src="{{ asset('storage/' . $authorProfile->profile_picture) }}" alt="{{ $author?->name }}" class="h-full w-full object-cover" />
                        @else
                            {{ Str::substr($author?->name ?? 'U', 0, 2) }}
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h3 class="truncate text-sm font-bold text-white">
                                {{ $authorProfile?->username ?? $author?->name }}
                            </h3>
                            @if($authorProfile?->is_verified)
                                <x-heroicon-s-check-circle class="size-4 theme-text" />
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </a>

                @if(auth()->check() && auth()->id() !== $post->user_id)
                    <button
                        wire:click="toggleFollow({{ $post->user_id }})"
                        type="button"
                        class="rounded-full border px-3 py-1.5 text-[11px] font-black uppercase tracking-wide transition {{ $isFollowing || $isFollowRequested ? 'border-slate-700 bg-slate-900 text-slate-200' : 'theme-btn text-black' }}">
                        {{ $isFollowing ? 'Following' : ($isFollowRequested ? 'Requested' : 'Follow') }}
                    </button>
                @endif
            </div>

            <div class="relative aspect-[4/5] w-full overflow-hidden bg-slate-900">
                @if($postImage)
                    <img src="{{ asset('storage/' . $postImage) }}" alt="Post content" class="h-full w-full object-cover" />
                @else
                    <div class="flex h-full w-full items-center justify-center text-sm font-bold uppercase tracking-[0.25em] text-slate-500">
                        Media
                    </div>
                @endif
            </div>

            <div class="space-y-4 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button wire:click="toggleLike({{ $post->id }})" type="button" class="flex items-center gap-2 text-slate-200 transition hover:text-white">
                            @if($isLiked)
                                <x-heroicon-s-heart class="size-6 text-rose-500" />
                            @else
                                <x-heroicon-o-heart class="size-6" />
                            @endif
                            <span class="text-xs font-bold">
                                {{ number_format(max((int) ($post->likes_count ?? 0), $post->likes->count())) }}
                            </span>
                        </button>

                        <button type="button" wire:click="openComments({{ $post->id }})" class="flex items-center gap-2 text-slate-200 transition hover:text-white">
                            <x-heroicon-o-chat-bubble-left class="size-5" />
                            <span class="text-xs font-bold">{{ max((int) ($post->comments_count ?? 0), $post->comments->count()) }}</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-4">
                        @auth
                            @if($post->user_id !== auth()->id())
                                <button type="button" wire:click="$dispatch('open-report', { type: 'post', id: {{ $post->id }} })" aria-label="Report post" class="text-slate-400 transition hover:text-white">
                                    <x-heroicon-o-flag class="size-5" />
                                </button>
                            @endif
                        @endauth
                        <button type="button" wire:click="toggleBookmark({{ $post->id }})"
                                aria-label="{{ $isBookmarked ? 'Remove saved post' : 'Save post' }}"
                                aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
                                class="transition hover:text-white {{ $isBookmarked ? 'theme-text' : 'text-slate-300' }}">
                            @if($isBookmarked)
                                <x-heroicon-s-bookmark class="size-5" />
                            @else
                                <x-heroicon-o-bookmark class="size-5" />
                            @endif
                        </button>
                    </div>
                </div>

                @if($post->content)
                    <p class="text-sm leading-6 text-slate-300">
                        <span class="mr-2 font-black text-white">{{ $authorProfile?->username ?? $author?->name }}</span>
                        {{ $post->content }}
                    </p>
                @endif

                @if($post->product)
                    <div class="theme-inner flex items-center justify-between gap-3 rounded-2xl border border-slate-800/80 p-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-900">
                                @if($productImage)
                                    <img src="{{ asset('storage/' . $productImage) }}" alt="{{ $post->product->name }}" class="h-full w-full object-cover" />
                                @else
                                    <span class="text-[10px] font-black uppercase text-slate-400">{{ Str::substr($post->product->name, 0, 2) }}</span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-xs font-bold text-white">{{ $post->product->name }}</p>
                                <p class="text-[11px] font-bold theme-text">₹{{ number_format((float) ($post->product->sale_price ?? $post->product->price), 0) }}</p>
                            </div>
                        </div>

                        <a href="{{ route('product.detail', $post->product->slug ?? $post->product->id) }}" class="theme-btn shrink-0 rounded-xl px-3.5 py-2 text-[11px] font-black uppercase tracking-wide">
                            Shop now
                        </a>
                    </div>
                @endif
            </div>
        </article>
    @empty
        <div class="theme-card rounded-lg border border-slate-800/80 p-10 text-center shadow-xl">
            <p class="text-3xl">📸</p>
            <h3 class="mt-4 text-lg font-black text-white">No posts available</h3>
            <p class="mt-2 text-sm text-slate-400">Start following creators or check back later.</p>
        </div>
    @endforelse

    @if($posts->hasPages())
        <div class="pb-6">
            {{ $posts->links() }}
        </div>
    @endif

    @if ($activeCommentsPostId)
        @php $activePost = $posts->firstWhere('id', $activeCommentsPostId); @endphp
        <div class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Comments" wire:keydown.escape.window="closeComments">
            <div class="absolute inset-0 bg-black/60" wire:click="closeComments"></div>

            <div class="theme-card absolute inset-x-0 bottom-0 flex max-h-[70vh] flex-col rounded-t-2xl border-t border-slate-800/80 pb-[env(safe-area-inset-bottom)] md:inset-x-auto md:bottom-6 md:right-6 md:w-96 md:rounded-2xl md:border md:pb-0">
                <header class="flex items-center justify-between border-b border-slate-800/80 px-4 py-3">
                    <h2 class="text-sm font-black text-white">Comments</h2>
                    <button type="button" wire:click="closeComments" aria-label="Close" class="rounded-lg p-1.5 text-slate-400 hover:bg-white/5 hover:text-white">
                        <x-heroicon-o-x-mark class="size-5" />
                    </button>
                </header>

                <ul class="flex-1 space-y-3 overflow-y-auto px-4 py-3">
                    @forelse ($activePost?->comments ?? [] as $comment)
                        <li class="flex items-start gap-2.5">
                            <span class="theme-soft-bg flex size-7 shrink-0 items-center justify-center rounded-full text-[11px] font-black text-white">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </span>
                            <p class="text-sm text-slate-200">
                                <span class="font-bold text-white">{{ $comment->user->name }}</span>
                                <span class="ml-1 text-slate-300">{{ $comment->content }}</span>
                            </p>
                        </li>
                    @empty
                        <li class="py-10 text-center text-xs text-slate-500">No comments yet. Be the first.</li>
                    @endforelse
                </ul>

                <form wire:submit="postComment" class="flex items-center gap-2 border-t border-slate-800/80 p-3">
                    <input wire:model="newComment" type="text" placeholder="Write a comment..." class="theme-inner min-w-0 flex-1 rounded-full border border-slate-700 px-4 py-2 text-sm text-white placeholder:text-slate-500 focus:border-(--accent-primary) focus:outline-none">
                    <button type="submit" aria-label="Post comment" class="theme-btn flex size-10 shrink-0 items-center justify-center rounded-full">
                        <x-heroicon-o-paper-airplane class="size-4" />
                    </button>
                </form>
                @error('newComment') <p class="px-3 pb-2 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>
        </div>
    @endif

    <livewire:report-content />
</div>