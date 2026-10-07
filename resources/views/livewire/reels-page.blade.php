<div id="reels-feed" class="w-full overflow-hidden bg-black" style="height: calc(100dvh - 118px);">
    <div class="flex h-full w-full">

        {{-- ========================================= --}}
        {{-- LEFT: SUGGESTIONS 35%                      --}}
        {{-- ========================================= --}}

        <aside class="hidden h-full w-[35%] shrink-0 overflow-y-auto border-r border-white/10 bg-[#0b0b0b] md:block">
            <div class="px-6 py-6 lg:px-8">

                <h2 class="text-lg font-black text-white">
                    Discover creators and follow their reels
                </h2>

                <livewire:follow-suggestions :limit="15" variant="list" />

            </div>
        </aside>


        {{-- ========================================= --}}
        {{-- RIGHT: REELS 65%                           --}}
        {{-- ========================================= --}}

        <main class="relative h-full min-w-0 flex-1 bg-black">

            {{-- Reel scroll --}}
            <div id="reel-scroll"
                class="flex h-full w-full snap-y snap-mandatory flex-col overflow-y-auto overflow-x-hidden"
                style="scrollbar-width: none;">

                @forelse ($reels as $reel)
                    @php
                        $videoUrl = $reel->video_path ? asset('storage/' . $reel->video_path) : null;

                        $posterUrl = $reel->thumbnail ? asset('storage/' . $reel->thumbnail) : null;

                        $avatar = $reel->user->profile?->profile_picture;

                        $liked = in_array($reel->id, $likedReelIds);

                        $bookmarked = auth()->check() && in_array($reel->id, $bookmarkedReelIds, true);

                        $followStatus = $followStatuses[$reel->user_id] ?? null;

                        $following = $followStatus === 'accepted';

                        $followRequested = $followStatus === 'pending';

                        $onSale =
                            $reel->product &&
                            (float) $reel->product->sale_price > 0 &&
                            (float) $reel->product->sale_price < (float) $reel->product->price;

                        $money = fn($v) => '₹' . number_format($v, 0);
                    @endphp


                    {{-- ======================================= --}}
                    {{-- ONE REEL                                 --}}
                    {{-- ======================================= --}}

                    <section wire:key="reel-{{ $reel->id }}" id="reel-{{ $reel->id }}"
                        data-reel="{{ $reel->id }}"
                        class="flex h-full min-h-full w-full shrink-0 snap-start items-center justify-center">

                        <div class="reel-tap relative h-[92%] max-h-[92%] aspect-[9/16] overflow-hidden rounded-2xl bg-black">

                            {{-- VIDEO --}}
                            @if ($videoUrl)
                                <video src="{{ $videoUrl }}"
                                    @if ($posterUrl) poster="{{ $posterUrl }}" @endif
                                    class="size-full cursor-pointer object-cover" playsinline muted loop
                                    preload="auto"></video>
                            @elseif ($posterUrl)
                                <img src="{{ $posterUrl }}" alt="" class="size-full object-cover">
                            @else
                                <div class="flex size-full items-center justify-center text-slate-600">
                                    <x-heroicon-o-film class="size-14" />
                                </div>
                            @endif

                            <div
                                class="reel-play-indicator pointer-events-none absolute inset-0 z-30 flex items-center justify-center opacity-0 transition-opacity duration-200"
                            >
                                <span class="reel-play-indicator-icon flex size-16 items-center justify-center rounded-full bg-black/50 text-white shadow-xl backdrop-blur-sm">
                                    <x-heroicon-s-play class="reel-play-icon size-8" />
                                    <x-heroicon-s-pause class="reel-pause-icon hidden size-8" />
                                </span>
                            </div>


                            {{-- GRADIENT --}}
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-black/50">
                            </div>


                            {{-- ================================= --}}
                            {{-- TOP CREATOR                         --}}
                            {{-- ================================= --}}

                            <div class="absolute inset-x-3 top-3 flex items-center gap-2">

                                <a href="{{ route('users.show', $reel->user) }}"
                                    class="flex min-w-0 flex-1 items-center gap-2">

                                    <span
                                        class="theme-soft-bg flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full ring-1 ring-white/30">

                                        @if ($avatar)
                                            <img src="{{ asset('storage/' . $avatar) }}" alt=""
                                                class="size-full object-cover">
                                        @else
                                            <span class="theme-text text-xs font-black">
                                                {{ strtoupper(substr($reel->user->name, 0, 1)) }}
                                            </span>
                                        @endif

                                    </span>

                                    <span class="truncate text-sm font-bold text-white">
                                        {{ $reel->user->name }}
                                    </span>

                                </a>


                                @auth

                                    @if (auth()->id() !== $reel->user_id)
                                        <button type="button" wire:click="toggleFollow({{ $reel->user_id }})"
                                            class="{{ $following || $followRequested ? 'border border-white/40 bg-black/30 text-white' : 'theme-btn' }}
                                                cursor-pointer rounded-full px-3 py-1 text-[11px] font-bold">
                                            {{ $following ? 'Following' : ($followRequested ? 'Requested' : 'Follow') }}
                                        </button>
                                    @endif

                                @endauth


                                <button type="button"
                                    class="mute-toggle flex size-8 cursor-pointer items-center justify-center rounded-full bg-black/40 text-white backdrop-blur">
                                    <x-heroicon-o-speaker-x-mark class="mute-icon-off size-4" />
                                    <x-heroicon-o-speaker-wave class="mute-icon-on hidden size-4" />
                                </button>

                            </div>


                            {{-- ================================= --}}
                            {{-- RIGHT ACTIONS                       --}}
                            {{-- ================================= --}}

                            <div class="absolute bottom-24 right-2 flex flex-col items-center gap-3 text-white">

                                {{-- LIKE --}}
                                <button type="button" wire:click="toggleLike({{ $reel->id }})"
                                    class="flex cursor-pointer flex-col items-center gap-1">
                                    <span
                                        class="flex size-10 items-center justify-center rounded-full bg-black/40 backdrop-blur">
                                        @if ($liked)
                                            <x-heroicon-s-heart class="size-5 text-rose-500" />
                                        @else
                                            <x-heroicon-o-heart class="size-5" />
                                        @endif
                                    </span>

                                    <span class="text-[11px] font-bold">
                                        {{ $reel->likes_count }}
                                    </span>
                                </button>


                                {{-- COMMENT --}}
                                <button type="button" wire:click="openComments({{ $reel->id }})"
                                    class="flex cursor-pointer flex-col items-center gap-1">
                                    <span
                                        class="flex size-10 items-center justify-center rounded-full bg-black/40 backdrop-blur">
                                        <x-heroicon-o-chat-bubble-oval-left class="size-5" />
                                    </span>

                                    <span class="text-[11px] font-bold">
                                        {{ $reel->comments_count }}
                                    </span>
                                </button>


                                {{-- SAVE --}}
                                <button type="button" wire:click="toggleBookmark({{ $reel->id }})"
                                    class="flex cursor-pointer flex-col items-center gap-1">
                                    <span
                                        class="flex size-10 items-center justify-center rounded-full bg-black/40 backdrop-blur">
                                        @if ($bookmarked)
                                            <x-heroicon-s-bookmark class="size-5" />
                                        @else
                                            <x-heroicon-o-bookmark class="size-5" />
                                        @endif
                                    </span>

                                    <span class="text-[11px] font-bold">
                                        Save
                                    </span>
                                </button>


                                {{-- REPORT --}}
                                @auth

                                    @if (auth()->id() !== $reel->user_id)
                                        <button type="button"
                                            wire:click="$dispatch('open-report', { type: 'reel', id: {{ $reel->id }} })"
                                            class="flex cursor-pointer flex-col items-center gap-1">
                                            <span
                                                class="flex size-10 items-center justify-center rounded-full bg-black/40 backdrop-blur">
                                                <x-heroicon-o-flag class="size-5" />
                                            </span>

                                            <span class="text-[11px] font-bold">
                                                Report
                                            </span>
                                        </button>
                                    @endif

                                @endauth


                                {{-- SHARE --}}
                                <button type="button" class="share-btn flex cursor-pointer flex-col items-center gap-1"
                                    data-url="{{ route('reels.index') }}#reel-{{ $reel->id }}">
                                    <span
                                        class="flex size-10 items-center justify-center rounded-full bg-black/40 backdrop-blur">
                                        <x-heroicon-o-share class="size-5" />
                                    </span>

                                    <span class="text-[11px] font-bold">
                                        Share
                                    </span>
                                </button>


                                {{-- VIEWS --}}
                                <div class="flex flex-col items-center gap-1 opacity-80">
                                    <span
                                        class="flex size-10 items-center justify-center rounded-full bg-black/40 backdrop-blur">
                                        <x-heroicon-o-eye class="size-5" />
                                    </span>

                                    <span class="text-[11px] font-bold">
                                        {{ $reel->views_count }}
                                    </span>
                                </div>

                            </div>


                            {{-- ================================= --}}
                            {{-- BOTTOM                               --}}
                            {{-- ================================= --}}

                            <div class="absolute inset-x-3 bottom-3 mr-12 text-white">

                                @if ($reel->caption)
                                    <p class="mb-2 line-clamp-2 text-xs leading-snug">
                                        {{ $reel->caption }}
                                    </p>
                                @endif


                                @if ($reel->product)
                                    <a href="{{ route('product.detail', $reel->product->slug) }}"
                                        class="flex items-center gap-2 rounded-xl border border-white/15 bg-black/50 p-2 backdrop-blur">

                                        <span
                                            class="theme-soft-bg flex size-8 shrink-0 items-center justify-center rounded-lg">
                                            <x-heroicon-o-shopping-bag class="theme-text size-4" />
                                        </span>

                                        <span class="min-w-0 flex-1">

                                            <span class="block truncate text-[11px] font-bold">
                                                {{ $reel->product->name }}
                                            </span>

                                            <span class="text-[10px] text-slate-300">
                                                {{ $money($reel->product->sale_price > 0 ? $reel->product->sale_price : $reel->product->price) }}
                                            </span>

                                        </span>

                                        <span class="theme-btn rounded-lg px-2 py-1 text-[10px] font-bold">
                                            Shop
                                        </span>

                                    </a>
                                @endif

                            </div>

                        </div>

                    </section>

                @empty

                    <div class="flex h-full items-center justify-center text-center text-slate-500">
                        <div>
                            <x-heroicon-o-film class="mx-auto size-12" />
                            <p class="mt-3 text-sm font-bold text-white">
                                No reels yet
                            </p>
                        </div>
                    </div>
                @endforelse

            </div>

        </main>

        @script
            <script>
                const initReelPlayer = () => {
                    function showPlayPauseIcon(section, isPaused) {

                        const indicator =
                            section.querySelector('.reel-play-indicator');

                        if (!indicator) {
                            return;
                        }

                        const playIcon =
                            indicator.querySelector('.reel-play-icon');

                        const pauseIcon =
                            indicator.querySelector('.reel-pause-icon');


                        if (isPaused) {

                            playIcon?.classList.add('hidden');
                            pauseIcon?.classList.remove('hidden');

                        } else {

                            playIcon?.classList.remove('hidden');
                            pauseIcon?.classList.add('hidden');

                        }


                        // Show
                        indicator.classList.remove('opacity-0');
                        indicator.classList.add('opacity-100');


                        // Clear previous timer
                        clearTimeout(
                            indicator._hideTimer
                        );


                        // Hide after 700ms
                        indicator._hideTimer = setTimeout(() => {

                            indicator.classList.remove('opacity-100');
                            indicator.classList.add('opacity-0');

                        }, 700);

                    }

                    const feed = document.getElementById('reels-feed');

                    if (!feed) {
                        console.log('Reel feed not found');
                        return;
                    }

                    const scrollBox = feed.querySelector('#reel-scroll');

                    if (!scrollBox) {
                        console.log('Reel scroll container not found');
                        return;
                    }

                    const sections = Array.from(
                        scrollBox.querySelectorAll('[data-reel]')
                    );

                    console.log('Reels found:', sections.length);


                    /*
                    |--------------------------------------------------------------------------
                    | Get video
                    |--------------------------------------------------------------------------
                    */

                    const getVideo = (section) => {
                        return section.querySelector('video');
                    };


                    /*
                    |--------------------------------------------------------------------------
                    | Stop every video
                    |--------------------------------------------------------------------------
                    */

                    const stopAll = (except = null) => {

                        sections.forEach(section => {

                            const video = getVideo(section);

                            if (!video || video === except) {
                                return;
                            }

                            video.pause();

                        });

                    };


                    /*
                    |--------------------------------------------------------------------------
                    | Play video
                    |--------------------------------------------------------------------------
                    */

                    const playVideo = (video) => {

                        if (!video) {
                            return;
                        }

                        stopAll(video);

                        //video.muted = true;

                        const promise = video.play();

                        if (promise) {
                            promise.catch(error => {
                                console.log('Play blocked:', error);
                            });
                        }

                    };


                    /*
                    |--------------------------------------------------------------------------
                    | Pause video
                    |--------------------------------------------------------------------------
                    */

                    const pauseVideo = (video) => {

                        if (!video) {
                            return;
                        }

                        video.pause();

                    };


                    /*
                    |--------------------------------------------------------------------------
                    | Intersection Observer
                    |--------------------------------------------------------------------------
                    */

                    const observer = new IntersectionObserver(
                        entries => {

                            entries.forEach(entry => {

                                const section = entry.target;
                                const video = getVideo(section);

                                if (!video) {
                                    return;
                                }


                                if (
                                    entry.isIntersecting &&
                                    entry.intersectionRatio >= 0.65
                                ) {

                                    playVideo(video);

                                } else {

                                    pauseVideo(video);

                                }

                            });

                        }, {
                            root: scrollBox,
                            threshold: [0.25, 0.65, 0.85]
                        }
                    );


                    sections.forEach(section => {

                        const video = getVideo(section);

                        if (!video) {
                            return;
                        }

                        video.muted = true;
                        video.playsInline = true;
                        video.loop = true;

                        observer.observe(section);

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | CLICK VIDEO = PLAY / PAUSE
                    |--------------------------------------------------------------------------
                    */

                    scrollBox.addEventListener('click', event => {

                        const video = event.target.closest('video');

                        if (!video) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();


                        const section =
                            video.closest('[data-reel]');


                        if (video.paused) {

                            playVideo(video);

                            showPlayPauseIcon(
                                section,
                                false
                            );

                        } else {

                            pauseVideo(video);

                            showPlayPauseIcon(
                                section,
                                true
                            );

                        }

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | MUTE BUTTON
                    |--------------------------------------------------------------------------
                    */

                    scrollBox.addEventListener('click', event => {

                        const button = event.target.closest('.mute-toggle');

                        if (!button) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();

                        const section = button.closest('[data-reel]');

                        if (!section) {
                            return;
                        }

                        const video = getVideo(section);

                        if (!video) {
                            return;
                        }


                        video.muted = !video.muted;


                        const offIcon =
                            button.querySelector('.mute-icon-off');

                        const onIcon =
                            button.querySelector('.mute-icon-on');


                        if (video.muted) {

                            offIcon?.classList.remove('hidden');
                            onIcon?.classList.add('hidden');

                            button.setAttribute(
                                'aria-label',
                                'Unmute'
                            );

                        } else {

                            offIcon?.classList.add('hidden');
                            onIcon?.classList.remove('hidden');

                            button.setAttribute(
                                'aria-label',
                                'Mute'
                            );

                        }

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | SPACE = PLAY / PAUSE
                    |--------------------------------------------------------------------------
                    */

                    document.addEventListener('keydown', event => {

                        const activeElement =
                            document.activeElement;

                        if (
                            activeElement &&
                            (
                                activeElement.tagName === 'INPUT' ||
                                activeElement.tagName === 'TEXTAREA' ||
                                activeElement.tagName === 'SELECT'
                            )
                        ) {
                            return;
                        }


                        if (event.code !== 'Space') {
                            return;
                        }

                        event.preventDefault();


                        const visibleSection =
                            getMostVisibleSection();

                        if (!visibleSection) {
                            return;
                        }


                        const video =
                            getVideo(visibleSection);

                        if (!video) {
                            return;
                        }


                        if (video.paused) {

                            playVideo(video);

                        } else {

                            pauseVideo(video);

                        }

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | M = MUTE / UNMUTE
                    |--------------------------------------------------------------------------
                    */

                    document.addEventListener('keydown', event => {

                        if (
                            event.key.toLowerCase() !== 'm'
                        ) {
                            return;
                        }


                        const activeElement =
                            document.activeElement;

                        if (
                            activeElement &&
                            (
                                activeElement.tagName === 'INPUT' ||
                                activeElement.tagName === 'TEXTAREA'
                            )
                        ) {
                            return;
                        }


                        const section =
                            getMostVisibleSection();

                        if (!section) {
                            return;
                        }


                        const video =
                            getVideo(section);

                        if (!video) {
                            return;
                        }


                        const button =
                            section.querySelector('.mute-toggle');


                        video.muted = !video.muted;


                        const offIcon =
                            button?.querySelector('.mute-icon-off');

                        const onIcon =
                            button?.querySelector('.mute-icon-on');


                        if (video.muted) {

                            offIcon?.classList.remove('hidden');
                            onIcon?.classList.add('hidden');

                        } else {

                            offIcon?.classList.add('hidden');
                            onIcon?.classList.remove('hidden');

                        }

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | FIND MOST VISIBLE REEL
                    |--------------------------------------------------------------------------
                    */

                    function getMostVisibleSection() {

                        let bestSection = null;
                        let bestRatio = 0;


                        sections.forEach(section => {

                            const rect =
                                section.getBoundingClientRect();

                            const containerRect =
                                scrollBox.getBoundingClientRect();


                            const visibleTop =
                                Math.max(
                                    rect.top,
                                    containerRect.top
                                );


                            const visibleBottom =
                                Math.min(
                                    rect.bottom,
                                    containerRect.bottom
                                );


                            const visibleHeight =
                                Math.max(
                                    0,
                                    visibleBottom - visibleTop
                                );


                            const ratio =
                                rect.height > 0 ?
                                visibleHeight / rect.height :
                                0;


                            if (ratio > bestRatio) {

                                bestRatio = ratio;
                                bestSection = section;

                            }

                        });


                        return bestSection;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PLAY CURRENT REEL ON FIRST LOAD
                    |--------------------------------------------------------------------------
                    */

                    setTimeout(() => {

                        const section =
                            getMostVisibleSection();

                        if (!section) {
                            return;
                        }

                        const video =
                            getVideo(section);

                        if (video) {
                            playVideo(video);
                        }

                    }, 300);

                };


                initReelPlayer();
            </script>
        @endscript

        <livewire:report-content />

    </div>
</div>
