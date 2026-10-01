<div>
    @if ($showViewer && $story)

        {{-- Full Screen Overlay --}}
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/95 p-4"
            wire:click.self="closeViewer"
        >

            {{-- Story Container --}}
            <div class="relative h-full max-h-[850px] w-full max-w-md overflow-hidden rounded-2xl bg-black">

                {{-- Close Button --}}
                <button
                    type="button"
                    wire:click="closeViewer"
                    class="cursor-pointer absolute right-4 top-4 z-30 flex size-10 items-center justify-center rounded-full bg-black/60 text-2xl leading-none text-white transition hover:bg-black/80"
                    aria-label="Close story"
                >
                    &times;
                </button>

                {{-- User Information --}}
                <a
                    href="{{ route('users.show', $story->user) }}"
                    class="absolute left-4 right-16 top-4 z-30 flex items-center gap-3"
                >

                    {{-- Profile Picture --}}
                    <div class="size-10 overflow-hidden rounded-full border-2 border-white bg-slate-900">

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

                    {{-- User Name + Time --}}
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-white">
                            {{ $story->user->name }}
                        </div>

                        <div class="text-xs text-slate-300">
                            {{ $story->created_at->diffForHumans() }}
                        </div>
                    </div>

                </a>

                {{-- Story Media --}}
                <div class="flex h-full w-full items-center justify-center">

                    @if ($story->media_type === 'image')

                        <img
                            src="{{ asset('storage/' . $story->media_path) }}"
                            alt="Story"
                            class="h-full w-full object-contain"
                        >

                    @elseif ($story->media_type === 'video')

                        <video
                            src="{{ asset('storage/' . $story->media_path) }}"
                            autoplay
                            controls
                            playsinline
                            class="h-full w-full object-contain"
                        ></video>

                    @endif

                </div>

                {{-- Caption --}}
                @if ($story->caption)

                    <div class="absolute bottom-16 left-4 right-4 z-20 rounded-xl bg-black/60 px-4 py-3 text-sm text-white backdrop-blur-sm">
                        {{ $story->caption }}
                    </div>

                @endif

                {{-- View Count --}}
                <button
                    type="button"
                    wire:click="$set('showViewers', true)"
                    class="cursor-pointer absolute bottom-4 left-4 z-20 flex items-center gap-2 text-xs font-semibold text-white transition hover:text-slate-300"
                >
                    <span>👁</span>
                    <span>{{ $story->views_count }} views</span>
                </button>

                {{-- Viewers Modal --}}
                @if ($showViewers)

                    <div
                        class="absolute inset-0 z-40 flex items-end justify-center bg-black/60"
                        wire:click.self="$set('showViewers', false)"
                    >
                        <div class="w-full rounded-t-2xl bg-slate-950 p-5 shadow-2xl">

                            {{-- Header --}}
                            <div class="mb-4 flex items-center justify-between">

                                <div>
                                    <h3 class="text-base font-bold text-white">
                                        Story Viewers
                                    </h3>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $story->views_count }} {{ Str::plural('view', $story->views_count) }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    wire:click="$set('showViewers', false)"
                                    class="cursor-pointer flex size-9 items-center justify-center rounded-full bg-slate-800 text-xl text-white transition hover:bg-slate-700"
                                >
                                    &times;
                                </button>

                            </div>

                            {{-- Viewers --}}
                            <div class="max-h-80 space-y-3 overflow-y-auto">

                                @forelse ($viewers as $viewer)

                                    <div class="flex items-center gap-3">

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

                                        {{-- Name + Time --}}
                                        <div class="min-w-0 flex-1">

                                            <div class="truncate text-sm font-semibold text-white">
                                                {{ $viewer['name'] }}
                                            </div>

                                            <div class="text-xs text-slate-400">
                                                {{ $viewer['viewed_at'] }}
                                            </div>

                                        </div>

                                    </div>

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