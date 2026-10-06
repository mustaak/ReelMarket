<div
    x-data="{
        timer: null,

        startTimer() {
            this.stopTimer();

            @if ($showViewer && $story && $story->media_type === 'image')
                this.timer = setTimeout(() => {
                    $wire.nextStory();
                }, 5000);
            @endif
        },

        stopTimer() {
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
        }
    }"
    x-on:story-changed.window="startTimer()"
    x-on:livewire:navigated.window="startTimer()"
    x-on:keydown.escape.window="$wire.closeViewer()"
    x-init="startTimer()"
>
    @if ($showViewer && $story)

        {{-- ========================================================= --}}
        {{-- STORY VIEWER BACKDROP --}}
        {{-- ========================================================= --}}

        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/95 p-2 sm:p-4"
            wire:click.self="closeViewer"
        >

            {{-- ===================================================== --}}
            {{-- STORY CONTAINER --}}
            {{-- ===================================================== --}}

            <div
                wire:key="story-viewer-{{ $story->id }}"
                class="relative h-[calc(100vh-1rem)] max-h-[850px] w-full max-w-[430px] overflow-hidden rounded-xl bg-black shadow-2xl sm:h-[calc(100vh-2rem)] sm:rounded-2xl"
            >

                {{-- ================================================= --}}
                {{-- STORY PROGRESS --}}
                {{-- ================================================= --}}

                <div class="absolute left-3 right-3 top-3 z-[60] flex gap-1">

                    @foreach ($userStories as $index => $userStory)

                        <div
                            class="relative h-1 flex-1 overflow-hidden rounded-full bg-white/30"
                            wire:key="story-progress-track-{{ $userStory['id'] ?? $index }}"
                        >

                            @if ($index < $currentStoryIndex)

                                {{-- Completed --}}
                                <div class="absolute inset-0 rounded-full bg-white"></div>

                            @elseif ($index === $currentStoryIndex)

                                @if ($story->media_type === 'image')

                                    {{-- Current image progress --}}
                                    <div
                                        wire:key="story-progress-{{ $story->id }}"
                                        class="story-progress absolute inset-y-0 left-0 rounded-full bg-white"
                                    ></div>

                                @else

                                    {{-- Video progress is controlled by video --}}
                                    <div class="absolute inset-0 rounded-full bg-white"></div>

                                @endif

                            @endif

                        </div>

                    @endforeach

                </div>


                {{-- ================================================= --}}
                {{-- TOP GRADIENT --}}
                {{-- ================================================= --}}

                <div
                    class="pointer-events-none absolute inset-x-0 top-0 z-40 h-32 bg-gradient-to-b from-black/75 via-black/30 to-transparent"
                ></div>


                {{-- ================================================= --}}
                {{-- STORY USER HEADER --}}
                {{-- ================================================= --}}

                <a
                    href="{{ route('users.show', $story->user) }}"
                    class="absolute left-4 right-16 top-8 z-50 flex items-center gap-3"
                >

                    {{-- Avatar --}}
                    <div class="size-10 shrink-0 overflow-hidden rounded-full border-2 border-white bg-slate-900">

                        @if (optional($story->user->profile)->profile_picture)

                            <img
                                src="{{ asset('storage/' . $story->user->profile->profile_picture) }}"
                                alt="{{ $story->user->name }}"
                                class="h-full w-full object-cover"
                            >

                        @else

                            <div class="flex h-full w-full items-center justify-center text-sm font-bold text-white">
                                {{ Str::substr($story->user->name, 0, 1) }}
                            </div>

                        @endif

                    </div>

                    {{-- User info --}}
                    <div class="min-w-0">

                        <div class="truncate text-sm font-semibold text-white drop-shadow">
                            {{ $story->user->name }}
                        </div>

                        <div class="text-xs text-white/70">
                            {{ $story->created_at->diffForHumans() }}
                        </div>

                    </div>

                </a>


                {{-- ================================================= --}}
                {{-- CLOSE BUTTON --}}
                {{-- ================================================= --}}

                <button
                    type="button"
                    wire:click="closeViewer"
                    class="absolute right-3 top-7 z-[60] flex size-10 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-md transition hover:bg-black/70"
                    aria-label="Close story"
                >
                    <x-heroicon-o-x-mark class="size-6" />
                </button>


                {{-- ================================================= --}}
                {{-- STORY MEDIA --}}
                {{-- ================================================= --}}

                <div class="relative h-full w-full bg-black">

                    @if ($story->media_type === 'image')

                        <img
                            wire:key="story-image-{{ $story->id }}"
                            src="{{ asset('storage/' . $story->media_path) }}"
                            alt="Story"
                            class="h-full w-full object-cover object-center"
                            x-init="startTimer()"
                        >

                    @elseif ($story->media_type === 'video')

                        <video
                            wire:key="story-video-{{ $story->id }}"
                            src="{{ asset('storage/' . $story->media_path) }}"
                            autoplay
                            muted
                            playsinline
                            class="h-full w-full object-cover object-center"
                            x-on:ended="$wire.nextStory()"
                        ></video>

                    @endif

                </div>


                {{-- ================================================= --}}
                {{-- LEFT TAP AREA / PREVIOUS --}}
                {{-- ================================================= --}}

                @if ($currentStoryIndex > 0)

                    <button
                        type="button"
                        wire:click="previousStory"
                        class="absolute inset-y-16 left-0 z-30 w-1/3 cursor-pointer"
                        aria-label="Previous story"
                    ></button>

                @endif


                {{-- ================================================= --}}
                {{-- RIGHT TAP AREA / NEXT --}}
                {{-- ================================================= --}}

                @if ($currentStoryIndex < count($userStories) - 1)

                    <button
                        type="button"
                        wire:click="nextStory"
                        class="absolute inset-y-16 right-0 z-30 w-1/3 cursor-pointer"
                        aria-label="Next story"
                    ></button>

                @endif


                {{-- ================================================= --}}
                {{-- VISIBLE NAVIGATION ARROWS --}}
                {{-- ================================================= --}}

                @if ($currentStoryIndex > 0)

                    <button
                        type="button"
                        wire:click="previousStory"
                        class="absolute left-3 top-1/2 z-40 hidden size-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-md transition hover:bg-black/70 sm:flex"
                        aria-label="Previous story"
                    >
                        <x-heroicon-o-chevron-left class="size-5" />
                    </button>

                @endif


                @if ($currentStoryIndex < count($userStories) - 1)

                    <button
                        type="button"
                        wire:click="nextStory"
                        class="absolute right-3 top-1/2 z-40 hidden size-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-md transition hover:bg-black/70 sm:flex"
                        aria-label="Next story"
                    >
                        <x-heroicon-o-chevron-right class="size-5" />
                    </button>

                @endif


                {{-- ================================================= --}}
                {{-- BOTTOM GRADIENT --}}
                {{-- ================================================= --}}

                <div
                    class="pointer-events-none absolute inset-x-0 bottom-0 z-20 h-40 bg-gradient-to-t from-black/80 via-black/30 to-transparent"
                ></div>


                {{-- ================================================= --}}
                {{-- CAPTION --}}
                {{-- ================================================= --}}

                @if ($story->caption)

                    <div class="absolute bottom-20 left-4 right-4 z-30">

                        <div class="rounded-xl bg-black/50 px-4 py-3 text-sm leading-5 text-white backdrop-blur-md">
                            {{ $story->caption }}
                        </div>

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- BOTTOM ACTION BAR --}}
                {{-- ================================================= --}}

                <div class="absolute bottom-4 left-4 right-4 z-40 flex items-center gap-3">

                    {{-- View count --}}
                    <button
                        type="button"
                        wire:click="$set('showViewers', true)"
                        class="flex items-center gap-1.5 text-xs font-semibold text-white transition hover:text-white/70"
                    >
                        <x-heroicon-o-eye class="size-5" />

                        <span>
                            {{ $story->views_count }}
                        </span>
                    </button>

                    <div class="flex-1"></div>

                    {{-- Like --}}
                    <button
                        type="button"
                        class="flex size-10 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-md transition hover:bg-white/10"
                        aria-label="Like story"
                    >
                        <x-heroicon-o-heart class="size-6" />
                    </button>

                    {{-- Share --}}
                    <button
                        type="button"
                        class="flex size-10 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-md transition hover:bg-white/10"
                        aria-label="Share story"
                    >
                        <x-heroicon-o-paper-airplane class="size-6" />
                    </button>

                </div>


                {{-- ================================================= --}}
                {{-- VIEWERS MODAL --}}
                {{-- ================================================= --}}

                @if ($showViewers)

                    <div
                        class="absolute inset-0 z-[100] flex items-end justify-center bg-black/60 backdrop-blur-sm"
                        wire:click.self="$set('showViewers', false)"
                    >

                        <div class="max-h-[70%] w-full rounded-t-2xl bg-slate-950 p-5 shadow-2xl">

                            {{-- Header --}}
                            <div class="mb-4 flex items-center justify-between">

                                <div>

                                    <h3 class="text-base font-bold text-white">
                                        Story Viewers
                                    </h3>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $story->views_count }}
                                        {{ Str::plural('view', $story->views_count) }}
                                    </p>

                                </div>

                                <button
                                    type="button"
                                    wire:click="$set('showViewers', false)"
                                    class="flex size-9 items-center justify-center rounded-full bg-slate-800 text-white transition hover:bg-slate-700"
                                    aria-label="Close viewers"
                                >
                                    <x-heroicon-o-x-mark class="size-5" />
                                </button>

                            </div>


                            {{-- Viewer list --}}
                            <div class="max-h-80 space-y-2 overflow-y-auto">

                                @forelse ($viewers as $viewer)

                                    <a
                                        href="{{ route('users.show', $viewer['id']) }}"
                                        class="flex items-center gap-3 rounded-xl p-2 transition hover:bg-white/5"
                                    >

                                        {{-- Profile --}}
                                        <div class="size-10 shrink-0 overflow-hidden rounded-full bg-slate-800">

                                            @if ($viewer['profile_picture'])

                                                <img
                                                    src="{{ asset('storage/' . $viewer['profile_picture']) }}"
                                                    alt="{{ $viewer['name'] }}"
                                                    class="h-full w-full object-cover"
                                                >

                                            @else

                                                <div class="flex h-full w-full items-center justify-center text-sm font-bold text-white">
                                                    {{ Str::substr($viewer['name'], 0, 1) }}
                                                </div>

                                            @endif

                                        </div>

                                        {{-- User info --}}
                                        <div class="min-w-0 flex-1">

                                            <div class="truncate text-sm font-semibold text-white">
                                                {{ $viewer['name'] }}
                                            </div>

                                            <div class="text-xs text-slate-400">
                                                {{ $viewer['viewed_at'] }}
                                            </div>

                                        </div>

                                    </a>

                                @empty

                                    <div class="py-8 text-center text-sm text-slate-400">
                                        No viewers yet.
                                    </div>

                                @endforelse

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    @endif
</div>


<style>
    @keyframes story-progress {
        from {
            width: 0%;
        }

        to {
            width: 100%;
        }
    }

    .story-progress {
        width: 0%;
        animation: story-progress 5s linear forwards;
    }
</style>