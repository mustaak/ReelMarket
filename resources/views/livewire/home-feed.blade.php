@php
    $slides = [
        ['tag' => 'Limited time offer', 'title' => 'Fresh picks,', 'accent' => 'best prices', 'sub' => 'Discover products from top brands and the creators you follow.', 'cta' => 'Shop now', 'route' => 'shop.index'],
        ['tag' => 'Shop by reels', 'title' => 'Watch it. Love it.', 'accent' => 'Buy it.', 'sub' => 'Tap any reel to shop the exact product you just saw.', 'cta' => 'Watch reels', 'route' => 'reels.index'],
        ['tag' => 'Creator picks', 'title' => 'Follow creators,', 'accent' => 'shop their favourites', 'sub' => 'Posts with product tags let you buy in one tap.', 'cta' => 'Explore shop', 'route' => 'shop.index'],
    ];

    $heroProducts = $trendingProducts->filter(fn ($p) => $p->images->isNotEmpty())->take(3)->values();
    $heroCount = $heroProducts->count();

    $trust = [
        ['icon' => 'heroicon-o-truck',        'title' => 'Fast delivery',    'sub' => 'Quick shipping'],
        ['icon' => 'heroicon-o-arrow-path',   'title' => 'Easy returns',     'sub' => 'Hassle-free'],
        ['icon' => 'heroicon-o-shield-check', 'title' => 'Secure payments',  'sub' => 'Safe checkout'],
        ['icon' => 'heroicon-o-video-camera', 'title' => 'Shop by reels',    'sub' => 'Watch and buy'],
    ];
@endphp

<div class="w-full space-y-6 md:space-y-8">

    {{-- ================= HERO ================= --}}
    <section wire:ignore
             x-data="{ i: 0, n: {{ count($slides) }}, t: null }"
             x-init="t = setInterval(() => i = (i + 1) % n, 5500)"
             class="theme-card overflow-hidden rounded-3xl border border-slate-800/80 shadow-xl">
        <div class="grid md:min-h-80 md:grid-cols-5">
            <div class="flex flex-col justify-center gap-4 p-6 sm:p-8 md:col-span-3 md:p-10">
                <div class="grid">
                @foreach($slides as $k => $s)
                    <div :class="{ 'opacity-100 visible': i === {{ $k }}, 'opacity-0 invisible pointer-events-none': i !== {{ $k }} }"
                         class="col-start-1 row-start-1 space-y-3 transition-opacity duration-500 {{ $k === 0 ? 'opacity-100 visible' : 'opacity-0 invisible pointer-events-none' }}">
                        <span class="theme-soft-bg theme-text inline-block rounded-lg px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.18em]">{{ $s['tag'] }}</span>
                        <h2 class="text-3xl font-black leading-tight text-white sm:text-4xl">
                            {{ $s['title'] }}<br><span class="theme-text">{{ $s['accent'] }}</span>
                        </h2>
                        <p class="max-w-md text-sm text-slate-400">{{ $s['sub'] }}</p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            <a href="{{ route($s['route']) }}" class="theme-btn inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-xs font-black uppercase tracking-wide">
                                {{ $s['cta'] }}
                                <x-heroicon-o-arrow-right class="size-4" />
                            </a>
                            <a href="{{ route('reels.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 px-5 py-2.5 text-xs font-bold text-slate-200 transition hover:border-slate-500 hover:text-white">
                                <x-heroicon-o-play class="size-4" />
                                Watch reels
                            </a>
                        </div>
                    </div>
                @endforeach
                </div>

                <div class="flex items-center gap-1.5 pt-2">
                    @foreach($slides as $k => $s)
                        <button type="button" @click="i = {{ $k }}" aria-label="Slide {{ $k + 1 }}"
                                :class="i === {{ $k }} ? 'w-5 bg-(--accent-primary)' : 'w-1.5 bg-slate-700'"
                                class="h-1.5 rounded-full transition-all"></button>
                    @endforeach
                </div>
            </div>

            <div class="theme-inner relative min-h-52 md:col-span-2">
                @if($heroCount > 0)
                    @foreach($heroProducts as $hp)
                        <a href="{{ route('product.detail', $hp->slug ?? $hp->id) }}"
                           :class="{ 'opacity-100 visible': i % {{ $heroCount }} === {{ $loop->index }}, 'opacity-0 invisible pointer-events-none': i % {{ $heroCount }} !== {{ $loop->index }} }"
                           class="absolute inset-0 block transition-opacity duration-500 {{ $loop->first ? 'opacity-100 visible' : 'opacity-0 invisible pointer-events-none' }}">
                            <img src="{{ asset('storage/' . $hp->images->first()->image) }}" alt="{{ $hp->name }}" class="h-full w-full object-cover">
                            <span class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-black/60 px-4 py-3">
                                <span class="truncate text-xs font-bold text-white">{{ $hp->name }}</span>
                                <span class="theme-text shrink-0 text-xs font-black">₹{{ number_format($hp->final_price, 0) }}</span>
                            </span>
                        </a>
                    @endforeach
                @else
                    <div class="flex h-full min-h-52 items-center justify-center text-slate-600">
                        <x-heroicon-o-shopping-bag class="size-16" />
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section>
        <div class="flex items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
            <div>
                <p class="theme-text text-[10px] font-bold uppercase tracking-[0.22em]">YourBrand</p>
                <h2 class="mt-1 text-2xl font-black text-white">Your feed</h2>
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
        </div>

        <div class="theme-card rounded-lg border border-slate-800/80 p-4 shadow-xl sm:p-5">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-300">Following</h3>
                <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $storyUsers->count() }} creators</span>
            </div>

            <div class="flex gap-3 overflow-x-auto pb-1 no-scrollbar">
                <div class="shrink-0">
                    <livewire:story.create-story />
                </div>
                @foreach($storyUsers as $sUser)
                    <button
                        type="button"
                        wire:key="story-user-{{ $sUser->id }}"
                        wire:click="$dispatch('open-story', { storyId: {{ $sUser->activeStories->first()->id }} })"
                        class="flex min-w-[72px] shrink-0 cursor-pointer flex-col items-center gap-2 text-center"
                    >
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
                    </button>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ================= TRUST STRIP ================= --}}
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach($trust as $item)
            <div class="theme-card flex items-center gap-3 rounded-2xl border border-slate-800/80 px-4 py-3">
                <span class="theme-soft-bg theme-text flex size-10 shrink-0 items-center justify-center rounded-xl">
                    <x-dynamic-component :component="$item['icon']" class="size-5" />
                </span>
                <div class="min-w-0">
                    <p class="truncate text-xs font-black text-white">{{ $item['title'] }}</p>
                    <p class="truncate text-[11px] text-slate-500">{{ $item['sub'] }}</p>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ================= CATEGORIES ================= --}}
    @if($categories->isNotEmpty())
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-black text-white">Shop by category</h2>
                <a href="{{ route('shop.index') }}" class="theme-text text-xs font-bold hover:underline">View all</a>
            </div>
            <div class="no-scrollbar flex gap-4 overflow-x-auto pb-1">
                @foreach($categories as $category)
                    <a href="{{ route('shop.index', ['selectedCategory' => $category->slug ?? $category->id]) }}"
                       wire:key="home-cat-{{ $category->id }}"
                       class="group flex w-20 shrink-0 flex-col items-center gap-2 text-center">
                        <span class="theme-card flex size-16 items-center justify-center overflow-hidden rounded-2xl border border-slate-800 text-slate-300 transition group-hover:border-(--accent-primary) group-hover:text-(--accent-text)">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
                            @else
                                <x-heroicon-o-tag class="size-6" />
                            @endif
                        </span>
                        <span class="w-full truncate text-[11px] font-semibold text-slate-300 group-hover:text-white">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ================= MAIN GRID ================= --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        <div class="min-w-0 space-y-8 lg:col-span-8">

            {{-- ---------- Trending products ---------- --}}
            @if($trendingProducts->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-lg font-black text-white">
                            <x-heroicon-o-fire class="theme-text size-5" />
                            Trending products
                        </h2>
                        <a href="{{ route('shop.index') }}" class="theme-text text-xs font-bold hover:underline">View all</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                        @foreach($trendingProducts as $product)
                            @php
                                $img = $product->images->first()?->image;
                                $hasSale = (float) $product->sale_price > 0 && (float) $product->sale_price < (float) $product->price;
                                $off = $hasSale ? (int) round((1 - ((float) $product->sale_price / (float) $product->price)) * 100) : 0;
                                $startingPrice = $product->variants->isNotEmpty()
                                    ? $product->variants->map(fn ($variant) => (float) ($variant->price ?? $product->final_price))->min()
                                    : (float) $product->final_price;
                                $inStock = $product->is_in_stock;
                            @endphp

                            <article wire:key="home-product-{{ $product->id }}" class="group theme-card flex flex-col overflow-hidden rounded-2xl border border-slate-800/80 transition hover:-translate-y-0.5 hover:border-slate-700">
                                <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="relative block aspect-square overflow-hidden bg-slate-950">
                                    @if($img)
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-600">
                                            <x-heroicon-o-photo class="size-8" />
                                        </div>
                                    @endif

                                    @if($hasSale)
                                        <span class="absolute left-2 top-2 rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-black text-white">{{ $off }}% off</span>
                                    @elseif($product->featured)
                                        <span class="theme-btn absolute left-2 top-2 rounded-full px-2 py-0.5 text-[10px] font-black">Featured</span>
                                    @endif

                                    @if(! $inStock)
                                        <span class="absolute right-2 top-2 rounded-full border border-slate-700 bg-slate-950/85 px-2 py-0.5 text-[10px] font-bold text-slate-300">Sold out</span>
                                    @endif
                                </a>

                                <div class="flex flex-1 flex-col gap-1.5 p-3">
                                    <span class="theme-text truncate text-[10px] font-black uppercase tracking-[0.16em]">
                                        {{ $product->brand?->name ?? ($product->category?->name ?? 'Store item') }}
                                    </span>
                                    <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="line-clamp-2 text-xs font-bold text-white hover:text-(--accent-text)">
                                        {{ $product->name }}
                                    </a>

                                    <div class="mt-auto pt-1">
                                        <p class="text-sm font-black text-white">
                                            {{ $product->variants->isNotEmpty() ? 'From ' : '' }}₹{{ number_format($startingPrice, 0) }}
                                            @if($hasSale)
                                                <span class="ml-1 text-[11px] font-medium text-slate-500 line-through">₹{{ number_format((float) $product->price, 0) }}</span>
                                            @endif
                                        </p>
                                    </div>

                                    <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="theme-btn mt-1 block rounded-xl py-2 text-center text-[11px] font-black uppercase tracking-wide">
                                        {{ $product->variants->isNotEmpty() ? 'Choose options' : 'View product' }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ---------- Trending reels ---------- --}}
            @if($trendingReels->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-lg font-black text-white">
                            <x-heroicon-o-play-circle class="theme-text size-5" />
                            Trending reels
                        </h2>
                        <a href="{{ route('reels.index') }}" class="theme-text text-xs font-bold hover:underline">Watch all</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                        @foreach($trendingReels as $reel)
                            <a href="{{ route('reels.index') }}" wire:key="home-reel-{{ $reel->id }}"
                               class="group theme-card relative block aspect-[3/4] overflow-hidden rounded-2xl border border-slate-800/80">
                                @if($reel->thumbnail)
                                    <img src="{{ asset('storage/' . $reel->thumbnail) }}" alt="Reel" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @endif
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <x-heroicon-s-play-circle class="size-11 text-white/85" />
                                </span>
                                <span class="absolute inset-x-0 bottom-0 space-y-0.5 bg-black/60 px-3 py-2">
                                    <span class="block truncate text-[11px] font-bold text-white">{{ $reel->user?->profile?->username ?? $reel->user?->name }}</span>
                                    @if($reel->product)
                                        <span class="theme-text block truncate text-[10px] font-semibold">{{ $reel->product->name }}</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ---------- Feed ---------- --}}
            <section class="mx-auto w-full max-w-5xl space-y-5 md:space-y-6">
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
                            <div class="flex min-w-0 items-center gap-3">

                                @if ($author?->activeStories?->isNotEmpty())
                                    <button
                                        type="button"
                                        wire:click="$dispatch('open-story', { storyId: {{ $author->activeStories->first()->id }} })"
                                        class="relative flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-full p-[2px] theme-btn"
                                        aria-label="View {{ $author?->name }}'s story"
                                    >
                                        <div class="flex size-full items-center justify-center overflow-hidden rounded-full bg-slate-950 text-sm font-black uppercase">
                                            @if($authorProfile?->profile_picture)
                                                <img
                                                    src="{{ asset('storage/' . $authorProfile->profile_picture) }}"
                                                    alt="{{ $author?->name }}"
                                                    class="h-full w-full object-cover"
                                                />
                                            @else
                                                {{ Str::substr($author?->name ?? 'U', 0, 2) }}
                                            @endif
                                        </div>
                                    </button>
                                @else
                                    <a
                                        href="{{ route('users.show', $author) }}"
                                        class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-full theme-btn text-sm font-black uppercase"
                                    >
                                        @if($authorProfile?->profile_picture)
                                            <img
                                                src="{{ asset('storage/' . $authorProfile->profile_picture) }}"
                                                alt="{{ $author?->name }}"
                                                class="h-full w-full object-cover"
                                            />
                                        @else
                                            {{ Str::substr($author?->name ?? 'U', 0, 2) }}
                                        @endif
                                    </a>
                                @endif

                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <a
                                            href="{{ route('users.show', $author) }}"
                                            class="truncate text-sm font-bold text-white"
                                        >
                                            {{ $authorProfile?->username ?? $author?->name }}
                                        </a>

                                        @if($authorProfile?->is_verified)
                                            <x-heroicon-s-check-circle class="size-4 theme-text" />
                                        @endif
                                    </div>

                                    <p class="text-[11px] text-slate-400">
                                        {{ $post->created_at->diffForHumans() }}
                                    </p>
                                </div>

                            </div>

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
            </section>
        </div>

        {{-- ================= RIGHT SIDEBAR ================= --}}
        <aside class="space-y-5 lg:col-span-4">
            <div class="space-y-5 lg:sticky lg:top-36">

                @if($dealProduct)
                    @php
                        $dealImg = $dealProduct->images->first()?->image;
                        $dealOff = (int) round((1 - ((float) $dealProduct->sale_price / (float) $dealProduct->price)) * 100);
                    @endphp
                    <div class="theme-card theme-border overflow-hidden rounded-2xl border p-4 shadow-xl">
                        <div class="theme-text flex items-center justify-between text-xs font-black uppercase tracking-[0.16em]">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-fire class="size-4" />
                                Deal of the day
                            </span>
                            <span class="rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-black text-white">{{ $dealOff }}% off</span>
                        </div>

                        <a href="{{ route('product.detail', $dealProduct->slug ?? $dealProduct->id) }}" class="theme-inner mt-3 block aspect-[4/3] overflow-hidden rounded-xl">
                            @if($dealImg)
                                <img src="{{ asset('storage/' . $dealImg) }}" alt="{{ $dealProduct->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-600">
                                    <x-heroicon-o-photo class="size-10" />
                                </div>
                            @endif
                        </a>

                        <p class="mt-3 line-clamp-2 text-sm font-black text-white">{{ $dealProduct->name }}</p>
                        <p class="mt-1 text-lg font-black text-white">
                            ₹{{ number_format($dealProduct->final_price, 0) }}
                            <span class="ml-1 text-xs font-medium text-slate-500 line-through">₹{{ number_format((float) $dealProduct->price, 0) }}</span>
                        </p>

                        <div wire:ignore
                             x-data="{
                                end: {{ $dealEndsAtMs }}, h: '00', m: '00', s: '00',
                                tick() {
                                    const d = Math.max(0, Math.floor((this.end - Date.now()) / 1000));
                                    this.h = String(Math.floor(d / 3600)).padStart(2, '0');
                                    this.m = String(Math.floor((d % 3600) / 60)).padStart(2, '0');
                                    this.s = String(d % 60).padStart(2, '0');
                                }
                             }"
                             x-init="tick(); setInterval(() => tick(), 1000)"
                             class="mt-3 flex items-center gap-2 text-xs">
                            <x-heroicon-o-clock class="size-4 text-slate-500" />
                            <span class="text-slate-500">Ends in</span>
                            <span class="theme-inner rounded-lg px-2 py-1 font-black tabular-nums text-white" x-text="h"></span>
                            <span class="theme-inner rounded-lg px-2 py-1 font-black tabular-nums text-white" x-text="m"></span>
                            <span class="theme-inner theme-text rounded-lg px-2 py-1 font-black tabular-nums" x-text="s"></span>
                        </div>

                        <a href="{{ route('product.detail', $dealProduct->slug ?? $dealProduct->id) }}" class="theme-btn mt-4 block rounded-xl py-2.5 text-center text-xs font-black uppercase tracking-wide">
                            Grab deal
                        </a>
                    </div>
                @endif

                <div class="theme-card rounded-2xl border border-slate-800/80 p-4 shadow-xl">
                    <livewire:follow-suggestions />
                </div>
            </div>
        </aside>
    </div>

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
    <livewire:story.story-viewer />
</div>