<div id="reels-feed"
     class="h-[calc(100dvh-4rem)] w-full snap-y snap-mandatory overflow-y-scroll scroll-smooth bg-black md:h-[calc(100dvh-4.5rem)]"
     style="scrollbar-width: none;">

    @auth
        <a href="{{ route('reels.create') }}" aria-label="Create reel" title="Create reel" class="theme-btn fixed right-4 top-20 z-40 flex size-11 items-center justify-center rounded-full shadow-lg md:right-8 md:top-24">
            <x-heroicon-o-plus class="size-5" />
        </a>
    @endauth

    @forelse ($reels as $reel)
        @php
            $videoUrl = $reel->video_path ? asset('storage/' . $reel->video_path) : null;
            $posterUrl = $reel->thumbnail ? asset('storage/' . $reel->thumbnail) : null;
            $avatar = $reel->user->profile?->profile_picture;
            $liked = in_array($reel->id, $likedReelIds);
            $followStatus = $followStatuses[$reel->user_id] ?? null;
            $following = $followStatus === 'accepted';
            $followRequested = $followStatus === 'pending';
            $onSale = $reel->product && (float) $reel->product->sale_price > 0 && (float) $reel->product->sale_price < (float) $reel->product->price;
            $money = fn ($v) => '₹' . number_format($v, 0);
        @endphp

        <section wire:key="reel-{{ $reel->id }}"
                 id="reel-{{ $reel->id }}"
                 data-reel="{{ $reel->id }}"
                 class="relative flex h-full w-full snap-start snap-always items-center justify-center">

            <div class="reel-tap relative h-full w-full overflow-hidden bg-slate-950 md:my-4 md:h-[calc(100%-2rem)] md:max-w-[420px] md:rounded-3xl md:shadow-2xl md:ring-1 md:ring-white/10">
                @if ($videoUrl)
                    <video src="{{ $videoUrl }}"
                           @if($posterUrl) poster="{{ $posterUrl }}" @endif
                           class="size-full object-cover"
                           playsinline loop muted preload="metadata"></video>
                @elseif ($posterUrl)
                    <img src="{{ $posterUrl }}" alt="" class="size-full object-cover">
                @else
                    <div class="flex size-full items-center justify-center text-slate-600">
                        <x-heroicon-o-film class="size-14" />
                    </div>
                @endif

                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/85 via-black/5 to-black/50"></div>

                <!-- Top: creator + follow -->
                <div class="pointer-events-auto absolute inset-x-3 top-[max(0.75rem,env(safe-area-inset-top))] flex items-center gap-2">
                    <a href="{{ route('users.show', $reel->user) }}" class="flex min-w-0 flex-1 items-center gap-2">
                        <span class="theme-soft-bg flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full ring-2 ring-white/25">
                            @if ($avatar)
                                <img src="{{ asset('storage/' . $avatar) }}" alt="" class="size-full object-cover">
                            @else
                                <span class="theme-text text-sm font-black">{{ strtoupper(substr($reel->user->name, 0, 1)) }}</span>
                            @endif
                        </span>
                        <span class="min-w-0 truncate text-sm font-bold text-white drop-shadow">{{ $reel->user->name }}</span>
                    </a>

                    @if (auth()->check() && auth()->id() !== $reel->user_id)
                        <button type="button" wire:click="toggleFollow({{ $reel->user_id }})"
                                class="{{ $following || $followRequested ? 'border border-white/50 text-white' : 'theme-btn' }} shrink-0 rounded-full px-3 py-1 text-[11px] font-extrabold">
                            {{ $following ? 'Following' : ($followRequested ? 'Requested' : 'Follow') }}
                        </button>
                    @endif

                    <button type="button" class="mute-toggle flex size-8 shrink-0 items-center justify-center rounded-full bg-black/35 text-white backdrop-blur-sm" aria-label="Mute or unmute">
                        <x-heroicon-o-speaker-x-mark class="mute-icon-off size-4" />
                        <x-heroicon-o-speaker-wave class="mute-icon-on hidden size-4" />
                    </button>
                </div>

                <!-- Right rail: actions -->
                <div class="pointer-events-auto absolute bottom-28 right-3 flex flex-col items-center gap-4 text-white">
                    <button type="button" wire:click="toggleLike({{ $reel->id }})" aria-label="Like" class="flex flex-col items-center gap-1">
                        <span class="flex size-11 items-center justify-center rounded-full bg-black/35 backdrop-blur-sm">
                            @if ($liked)
                                <x-heroicon-s-heart class="size-6 text-rose-500" />
                            @else
                                <x-heroicon-o-heart class="size-6" />
                            @endif
                        </span>
                        <span class="text-xs font-bold drop-shadow">{{ $reel->likes_count }}</span>
                    </button>

                    <button type="button" wire:click="openComments({{ $reel->id }})" aria-label="Comments" class="flex flex-col items-center gap-1">
                        <span class="flex size-11 items-center justify-center rounded-full bg-black/35 backdrop-blur-sm">
                            <x-heroicon-o-chat-bubble-oval-left class="size-6" />
                        </span>
                        <span class="text-xs font-bold drop-shadow">{{ $reel->comments_count }}</span>
                    </button>

                    <button type="button" class="share-btn flex flex-col items-center gap-1" data-url="{{ route('reels.index') }}#reel-{{ $reel->id }}" aria-label="Share">
                        <span class="flex size-11 items-center justify-center rounded-full bg-black/35 backdrop-blur-sm">
                            <x-heroicon-o-share class="size-6" />
                        </span>
                        <span class="text-xs font-bold drop-shadow">Share</span>
                    </button>

                    <div class="flex flex-col items-center gap-1 opacity-80">
                        <span class="flex size-11 items-center justify-center rounded-full bg-black/35 backdrop-blur-sm">
                            <x-heroicon-o-eye class="size-6" />
                        </span>
                        <span class="text-xs font-bold drop-shadow">{{ $reel->views_count }}</span>
                    </div>
                </div>

                <!-- Bottom: caption + shoppable product -->
                <div class="pointer-events-auto absolute inset-x-3 bottom-[max(1rem,env(safe-area-inset-bottom))] mr-16 text-white">
                    @if ($reel->caption)
                        <p class="mb-2 line-clamp-2 text-sm leading-snug drop-shadow">{{ $reel->caption }}</p>
                    @endif

                    @if ($reel->product)
                        <a href="{{ route('product.detail', $reel->product->slug) }}"
                           class="flex items-center gap-2 rounded-xl border border-white/15 bg-black/40 p-2 backdrop-blur-sm">
                            <span class="theme-soft-bg flex size-9 shrink-0 items-center justify-center rounded-lg">
                                <x-heroicon-o-shopping-bag class="theme-text size-5" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-xs font-bold">{{ $reel->product->name }}</span>
                                <span class="text-[11px] text-slate-300">
                                    {{ $money($reel->product->sale_price > 0 ? $reel->product->sale_price : $reel->product->price) }}
                                    @if ($onSale)
                                        <s class="ml-1 text-slate-500">{{ $money($reel->product->price) }}</s>
                                    @endif
                                </span>
                            </span>
                            <span class="theme-btn shrink-0 rounded-lg px-2.5 py-1 text-[11px] font-extrabold">Shop</span>
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @empty
        <div class="flex h-full items-center justify-center px-6 text-center text-slate-400">
            <div>
                <x-heroicon-o-film class="mx-auto mb-3 size-12 text-slate-600" />
                <p class="font-bold text-white">No reels yet</p>
                <p class="mt-1 text-sm">Check back soon.</p>
            </div>
        </div>
    @endforelse

    <!-- Comments panel -->
    @if ($activeCommentsReelId)
        @php $activeReel = $reels->firstWhere('id', $activeCommentsReelId); @endphp
        <div class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Comments"
             wire:keydown.escape.window="closeComments">
            <div class="absolute inset-0 bg-black/60" wire:click="closeComments"></div>

            <div class="theme-card absolute inset-x-0 bottom-0 flex max-h-[70vh] flex-col rounded-t-2xl border-t border-slate-800/80 pb-[env(safe-area-inset-bottom)] md:inset-x-auto md:bottom-6 md:right-6 md:w-96 md:rounded-2xl md:border md:pb-0">
                <header class="flex items-center justify-between border-b border-slate-800/80 px-4 py-3">
                    <h2 class="text-sm font-black text-white">Comments</h2>
                    <button type="button" wire:click="closeComments" aria-label="Close" class="rounded-lg p-1.5 text-slate-400 hover:bg-white/5 hover:text-white">
                        <x-heroicon-o-x-mark class="size-5" />
                    </button>
                </header>

                <ul class="flex-1 space-y-3 overflow-y-auto px-4 py-3">
                    @forelse ($activeReel?->comments ?? [] as $comment)
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
                    <input wire:model="newComment" type="text" placeholder="Add a comment..."
                           class="theme-inner min-w-0 flex-1 rounded-full border border-slate-700 px-4 py-2 text-sm text-white placeholder:text-slate-500 focus:border-(--accent-primary) focus:outline-none">
                    <button type="submit" aria-label="Post comment"
                            class="theme-btn flex size-10 shrink-0 items-center justify-center rounded-full">
                        <x-heroicon-o-paper-airplane class="size-4" />
                    </button>
                </form>
                @error('newComment') <p class="px-3 pb-2 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>
        </div>
    @endif
</div>

<script>
    (function () {
        const init = () => {
            const items = document.querySelectorAll('[data-reel]');
            if (! items.length) return;

            const seen = new Set();

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    const video = entry.target.querySelector('video');
                    const id = entry.target.dataset.reel;

                    if (entry.isIntersecting && entry.intersectionRatio > 0.6) {
                        video?.play().catch(() => {});

                        if (! seen.has(id)) {
                            seen.add(id);
                            window.Livewire?.dispatch('reel-viewed', { reelId: Number(id) });
                        }
                    } else {
                        video?.pause();
                    }
                });
            }, { threshold: [0, 0.6, 1] });

            items.forEach((el) => observer.observe(el));

            items.forEach((el) => {
                const video = el.querySelector('video');
                const muteBtn = el.querySelector('.mute-toggle');
                const shareBtn = el.querySelector('.share-btn');

                if (video && muteBtn) {
                    const sync = () => {
                        muteBtn.querySelector('.mute-icon-off').classList.toggle('hidden', ! video.muted);
                        muteBtn.querySelector('.mute-icon-on').classList.toggle('hidden', video.muted);
                    };

                    muteBtn.addEventListener('click', (e) => { e.stopPropagation(); video.muted = ! video.muted; sync(); });
                    el.querySelector('.reel-tap').addEventListener('click', (e) => {
                        if (e.target.closest('button, a')) return;
                        video.muted = ! video.muted;
                        sync();
                    });
                }

                if (shareBtn) {
                    shareBtn.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        const url = shareBtn.dataset.url;

                        if (navigator.share) {
                            navigator.share({ url }).catch(() => {});
                        } else {
                            await navigator.clipboard.writeText(url);
                            window.Livewire?.dispatch('notify', { type: 'success', message: 'Link copied!' });
                        }
                    });
                }
            });
        };

        document.addEventListener('livewire:navigated', init);
        document.addEventListener('livewire:initialized', init);
    })();
</script>