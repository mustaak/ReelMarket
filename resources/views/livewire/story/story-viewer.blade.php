<div x-data="{
    timer: null,

    startTimer() {
        this.stopTimer();

        @if ($showViewer && $story && $story->media_type === 'image') this.timer = setTimeout(() => {
                    $wire.nextStory();
                }, 5000); @endif
    },

    stopTimer() {
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
    }
}" x-on:story-changed.window="startTimer()" x-on:livewire:navigated.window="startTimer()"
    x-init="startTimer()" x-on:keydown.escape.window="$wire.closeViewer()">
    @if ($showViewer && $story)

        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/95 p-4"
            wire:click.self="closeViewer">
            <div class="relative h-full max-h-[850px] w-full max-w-md overflow-hidden rounded-2xl bg-black">
                {{-- ===================================================== --}}
                {{-- STORY PROGRESS --}}
                {{-- ===================================================== --}}
                <div class="absolute left-3 right-3 top-3 z-50 flex gap-1.5">
                    @foreach ($userStories as $index => $userStory)
                        <div class="relative h-1 flex-1 overflow-hidden rounded-full bg-white/30">
                            @if ($index < $currentStoryIndex)
                                <div class="absolute inset-0 rounded-full bg-white"></div>
                            @elseif ($index === $currentStoryIndex)
                                @if ($story->media_type === 'image')
                                    <div class="story-progress absolute inset-y-0 left-0 rounded-full bg-white"></div>
                                @else
                                    <div class="absolute inset-0 rounded-full bg-white"></div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
                {{-- ===================================================== --}}
                {{-- CLOSE BUTTON --}}
                {{-- ===================================================== --}}
                <button type="button" wire:click="closeViewer"
                    class="absolute right-4 top-7 z-50 flex size-10 cursor-pointer items-center justify-center rounded-full bg-black/60 text-2xl leading-none text-white transition hover:bg-black/80"
                    aria-label="Close story">
                    &times;
                </button>
                {{-- ===================================================== --}}
                {{-- STORY USER --}}
                {{-- ===================================================== --}}
                <a href="{{ route('users.show', $story->user) }}"
                    class="absolute left-4 right-16 top-7 z-40 flex items-center gap-3">
                    <div class="size-10 overflow-hidden rounded-full border-2 border-white bg-slate-900">
                        @if (optional($story->user->profile)->profile_picture)
                            <img src="{{ asset('storage/' . $story->user->profile->profile_picture) }}"
                                alt="{{ $story->user->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-sm font-bold text-white">
                                {{ Str::substr($story->user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-white">
                            {{ $story->user->name }}
                        </div>
                        <div class="text-xs text-slate-300">
                            {{ $story->created_at->diffForHumans() }}
                        </div>
                    </div>
                </a>

                {{-- ===================================================== --}}
                {{-- STORY MEDIA --}}
                {{-- ===================================================== --}}

                <div class="flex h-full w-full items-center justify-center">

                    @if ($story->media_type === 'image')
                        <img wire:key="story-image-{{ $story->id }}"
                            src="{{ asset('storage/' . $story->media_path) }}" alt="Story"
                            class="h-full w-full object-contain" x-init="startTimer()">
                    @elseif ($story->media_type === 'video')
                        <video wire:key="story-video-{{ $story->id }}"
                            src="{{ asset('storage/' . $story->media_path) }}" autoplay controls playsinline
                            class="h-full w-full object-contain" x-on:ended="$wire.nextStory()"></video>
                    @endif

                </div>


                {{-- ===================================================== --}}
                {{-- PREVIOUS --}}
                {{-- ===================================================== --}}

                @if ($currentStoryIndex > 0)
                    <button type="button" wire:click="previousStory"
                        class="absolute left-3 top-1/2 z-30 flex size-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-black/50 text-2xl text-white backdrop-blur-sm transition hover:bg-black/70"
                        aria-label="Previous story">
                        ‹
                    </button>
                @endif


                {{-- ===================================================== --}}
                {{-- NEXT --}}
                {{-- ===================================================== --}}

                @if ($currentStoryIndex < count($userStories) - 1)
                    <button type="button" wire:click="nextStory"
                        class="absolute right-3 top-1/2 z-30 flex size-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-black/50 text-2xl text-white backdrop-blur-sm transition hover:bg-black/70"
                        aria-label="Next story">
                        ›
                    </button>
                @endif


                {{-- ===================================================== --}}
                {{-- CAPTION --}}
                {{-- ===================================================== --}}

                @if ($story->caption)
                    <div
                        class="absolute bottom-16 left-4 right-4 z-20 rounded-xl bg-black/60 px-4 py-3 text-sm text-white backdrop-blur-sm">
                        {{ $story->caption }}
                    </div>
                @endif


                {{-- ===================================================== --}}
                {{-- VIEW COUNT --}}
                {{-- ===================================================== --}}

                <button type="button" wire:click="$set('showViewers', true)"
                    class="absolute bottom-4 left-4 z-30 flex cursor-pointer items-center gap-2 text-xs font-semibold text-white transition hover:text-slate-300">

                    <span>👁</span>

                    <span>
                        {{ $story->views_count }}
                        {{ Str::plural('view', $story->views_count) }}
                    </span>

                </button>


                {{-- ===================================================== --}}
                {{-- VIEWERS MODAL --}}
                {{-- ===================================================== --}}

                @if ($showViewers)

                    <div class="absolute inset-0 z-[60] flex items-end justify-center bg-black/60"
                        wire:click.self="$set('showViewers', false)">

                        <div class="w-full rounded-t-2xl bg-slate-950 p-5 shadow-2xl">

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


                                <button type="button" wire:click="$set('showViewers', false)"
                                    class="flex size-9 cursor-pointer items-center justify-center rounded-full bg-slate-800 text-xl text-white transition hover:bg-slate-700"
                                    aria-label="Close viewers">
                                    &times;
                                </button>

                            </div>


                            {{-- Viewer List --}}

                            <div class="max-h-80 space-y-3 overflow-y-auto">

                                @forelse ($viewers as $viewer)
                                    <a href="{{ route('users.show', $viewer['id']) }}"
                                        class="flex cursor-pointer items-center gap-3 rounded-lg p-1 transition hover:bg-white/5">

                                        {{-- Profile --}}

                                        <div class="size-10 shrink-0 overflow-hidden rounded-full bg-slate-800">

                                            @if ($viewer['profile_picture'])
                                                <img src="{{ asset('storage/' . $viewer['profile_picture']) }}"
                                                    alt="{{ $viewer['name'] }}" class="h-full w-full object-cover">
                                            @else
                                                <div
                                                    class="flex h-full w-full items-center justify-center text-sm font-bold text-white">
                                                    {{ Str::substr($viewer['name'], 0, 1) }}
                                                </div>
                                            @endif

                                        </div>


                                        {{-- User Info --}}

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
        animation: story-progress 5s linear forwards;
    }
</style>
