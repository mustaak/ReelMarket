<main class="mx-auto w-full max-w-6xl space-y-8 pb-10">
    <header class="flex items-end justify-between border-b border-slate-800/80 pb-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] theme-text">Your library</p>
            <h1 class="mt-1 text-2xl font-black text-white">Saved content</h1>
        </div>
        <a href="{{ route('home') }}" class="text-xs font-bold theme-text hover:underline">Back to feed</a>
    </header>

    <section class="space-y-4" aria-labelledby="saved-posts-heading">
        <h2 id="saved-posts-heading" class="text-sm font-black uppercase tracking-wider text-slate-300">Posts</h2>
        @if($posts->isEmpty())
            <p class="py-8 text-sm text-slate-500">No saved posts.</p>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    <article wire:key="saved-post-{{ $post->id }}" class="theme-card overflow-hidden rounded-xl border border-slate-800/80">
                        @if($post->image || $post->images->isNotEmpty())
                            <img src="{{ asset('storage/' . ($post->image ?: $post->images->first()->image_path)) }}" alt="Post by {{ $post->user->name }}" class="aspect-[4/3] w-full object-cover">
                        @endif
                        <div class="space-y-3 p-4">
                            <p class="text-xs font-bold text-slate-400">{{ $post->user->profile?->username ?? $post->user->name }}</p>
                            @if($post->content)
                                <p class="line-clamp-4 text-sm leading-6 text-slate-200">{{ $post->content }}</p>
                            @endif
                            <div class="flex items-center justify-between gap-3">
                                <a href="{{ route('users.show', $post->user) }}" class="text-xs font-bold theme-text hover:underline">View creator</a>
                                <button type="button" wire:click="removePostBookmark({{ $post->id }})" class="text-xs font-bold text-slate-400 hover:text-white">Remove</button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="space-y-4" aria-labelledby="saved-reels-heading">
        <h2 id="saved-reels-heading" class="text-sm font-black uppercase tracking-wider text-slate-300">Reels</h2>
        @if($reels->isEmpty())
            <p class="py-8 text-sm text-slate-500">No saved reels.</p>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($reels as $reel)
                    <article wire:key="saved-reel-{{ $reel->id }}" class="theme-card space-y-3 rounded-xl border border-slate-800/80 p-3">
                        <a href="{{ route('reels.index', ['reel' => $reel->id]) }}" class="block overflow-hidden rounded-lg">
                            @if($reel->thumbnail)
                                <img src="{{ asset('storage/' . $reel->thumbnail) }}" alt="Reel by {{ $reel->user->name }}" class="aspect-[9/14] w-full object-cover">
                            @else
                                <span class="flex aspect-[9/14] items-center justify-center bg-slate-950 text-sm font-bold text-slate-400">Open reel</span>
                            @endif
                        </a>
                        <p class="line-clamp-2 text-sm text-slate-200">{{ $reel->caption }}</p>
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('reels.index', ['reel' => $reel->id]) }}" class="text-xs font-bold theme-text hover:underline">Watch reel</a>
                            <button type="button" wire:click="removeReelBookmark({{ $reel->id }})" class="text-xs font-bold text-slate-400 hover:text-white">Remove</button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</main>
