<main class="mx-auto w-full max-w-6xl space-y-6 pb-10 theme-card p-6">
    <header class="flex items-end justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] theme-text">YourBrand</p>
            <h1 class="mt-1 font-display text-2xl font-semibold text-white">Create a reel</h1>
        </div>
        <a href="{{ route('reels.index') }}" class="text-xs font-semibold text-slate-400 transition hover:text-white">Cancel</a>
    </header>

    <form wire:submit="publish" class="space-y-7">
        <section>
            <label for="video" class="mb-2 block text-xs font-semibold text-slate-300">Video <span class="text-rose-400">*</span></label>
            <div class="theme-inner rounded-lg border border-dashed border-slate-700 p-5">
                <input id="video" type="file" wire:model="video" accept="video/mp4,video/webm,video/quicktime" class="block w-full cursor-pointer text-xs text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white">
                <p class="mt-2 text-[11px] text-slate-500">MP4, WebM, or MOV · maximum 100 MB</p>
                <div wire:loading wire:target="video" class="mt-3 text-xs theme-text">Uploading video…</div>
            </div>
            @error('video') <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p> @enderror
        </section>

        <section>
            <label for="thumbnail" class="mb-2 block text-xs font-semibold text-slate-300">Cover image <span class="font-normal text-slate-500">Optional</span></label>
            <div class="theme-inner flex flex-wrap items-center gap-4 rounded-lg border border-slate-700/80 p-4">
                @if($thumbnail)
                    <img src="{{ $thumbnail->temporaryUrl() }}" alt="Reel cover preview" class="size-20 rounded-md object-cover">
                @else
                    <span class="flex size-20 items-center justify-center rounded-md bg-black/20 text-slate-500">
                        <x-heroicon-o-photo class="size-7" />
                    </span>
                @endif
                <input id="thumbnail" type="file" wire:model="thumbnail" accept="image/jpeg,image/png,image/webp" class="min-w-0 flex-1 text-xs text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white">
            </div>
            @error('thumbnail') <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p> @enderror
        </section>

        <section>
            <label for="caption" class="mb-2 block text-xs font-semibold text-slate-300">Caption</label>
            <textarea id="caption" wire:model="caption" rows="3" maxlength="2200" class="theme-inner w-full resize-y rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-slate-500 focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20" placeholder="Write a caption..."></textarea>
            @error('caption') <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p> @enderror
        </section>

        <section>
            <label for="productId" class="mb-2 block text-xs font-semibold text-slate-300">Tag a product <span class="font-normal text-slate-500">Optional</span></label>
            <select id="productId" wire:model="productId" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20">
                <option value="">No product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            @error('productId') <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p> @enderror
        </section>

        <footer class="flex justify-end border-t border-slate-800/80 pt-5">
            <button type="submit" wire:loading.attr="disabled" wire:target="publish,video,thumbnail" class="theme-btn inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold disabled:opacity-50">
                <x-heroicon-o-play class="size-4" />
                <span wire:loading.remove wire:target="publish">Publish reel</span>
                <span wire:loading wire:target="publish">Publishing…</span>
            </button>
        </footer>
    </form>
</main>
