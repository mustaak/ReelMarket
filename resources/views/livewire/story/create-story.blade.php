<div>
    {{-- Add Story Button --}}
    {{-- Your Story --}}
    <button
        type="button"
        wire:click="$set('showModal', true)"
        class="cursor-pointer flex min-w-[72px] shrink-0 flex-col items-center gap-2 text-center">
        <div class="relative size-16 rounded-full border-2 border-dashed border-slate-400">
            <div class="flex h-full w-full items-center justify-center rounded-full bg-slate-950">
                <span class="text-2xl font-light text-slate-500">
                    +
                </span>
            </div>
        </div>

        <span class="max-w-[70px] truncate text-[11px] font-semibold text-slate-300">
            Your Story
        </span>
    </button>

    {{-- Create Story Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
            wire:click.self="$set('showModal', false)">
            {{-- Modal Box --}}
            <div class="w-full max-w-md rounded-2xl bg-slate-950 p-6 shadow-2xl">

                {{-- Header --}}
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white">
                        Add to Story
                    </h2>

                    <button type="button" wire:click="$set('showModal', false)"
                        class="text-2xl leading-none text-slate-400 transition hover:text-white">
                        &times;
                    </button>
                </div>

                {{-- Photo / Video --}}
                <div class="grid grid-cols-2 gap-4">

                    {{-- Photo --}}
                    <label
                        class="cursor-pointer rounded-xl border border-slate-700 p-6 text-center text-white transition hover:border-slate-400 hover:bg-slate-900">
                        <div class="mb-2 text-3xl">
                            📷
                        </div>

                        <div class="font-medium">
                            Photo
                        </div>

                        <div class="mt-1 text-sm text-slate-400">
                            Upload a photo
                        </div>

                        <input type="file" wire:model="media" accept="image/*" class="hidden">
                    </label>

                    {{-- Video --}}
                    <label
                        class="cursor-pointer rounded-xl border border-slate-700 p-6 text-center text-white transition hover:border-slate-400 hover:bg-slate-900">
                        <div class="mb-2 text-3xl">
                            🎥
                        </div>

                        <div class="font-medium">
                            Video
                        </div>

                        <div class="mt-1 text-sm text-slate-400">
                            Upload a video
                        </div>

                        <input type="file" wire:model="media" accept="video/*" class="hidden">
                    </label>

                    {{-- Selected Media Preview --}}
                    @if ($media)
                        <div class="mt-5">

                            <div class="mb-2 text-sm font-semibold text-slate-300">
                                Preview
                            </div>

                            @if (str_starts_with($media->getMimeType(), 'image/'))
                                <img
                                    src="{{ $media->temporaryUrl() }}"
                                    alt="Story preview"
                                    class="mx-auto max-h-80 w-full rounded-xl object-contain"
                                >
                            @elseif (str_starts_with($media->getMimeType(), 'video/'))
                                <video
                                    src="{{ $media->temporaryUrl() }}"
                                    controls
                                    class="mx-auto max-h-80 w-full rounded-xl object-contain"
                                ></video>
                            @endif

                            <textarea
                                wire:model="caption"
                                placeholder="Add a caption..."
                                rows="3"
                                class="mt-4 w-full rounded-xl border border-slate-700 bg-slate-900 p-3 text-sm text-white placeholder-slate-500 focus:border-slate-500 focus:outline-none"
                            ></textarea>

                            <button
                                type="button"
                                wire:click="createStory"
                                wire:loading.attr="disabled"
                                class="mt-4 w-full rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-950 transition hover:bg-slate-200 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="createStory">
                                    Share Story
                                </span>

                                <span wire:loading wire:target="createStory">
                                    Sharing...
                                </span>
                            </button>

                        </div>
                    @endif

                </div>

            </div>
        </div>
    @endif
</div>
