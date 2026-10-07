@php
    $heroProducts = $trendingProducts->filter(fn($p) => $p->images->isNotEmpty())->take(3)->values();
    $heroCount = $heroProducts->count();

    $trust = [
        ['icon' => 'heroicon-o-truck', 'title' => 'Fast delivery', 'sub' => 'Quick shipping'],
        ['icon' => 'heroicon-o-arrow-path', 'title' => 'Easy returns', 'sub' => 'Hassle-free'],
        ['icon' => 'heroicon-o-shield-check', 'title' => 'Secure payments', 'sub' => 'Safe checkout'],
        ['icon' => 'heroicon-o-video-camera', 'title' => 'Shop by reels', 'sub' => 'Watch and buy'],
    ];
@endphp

<div class="w-full space-y-6 md:space-y-8">

    {{-- ================= HERO / BANNER SLIDER ================= --}}
    {{-- ========================================================= --}}
    {{-- HERO: BANNER SLIDER + REEL PROMOTION                      --}}
    {{-- ========================================================= --}}

    <section wire:ignore x-data="{
        banner: 0,
        bannerCount: {{ $slides->count() }},
        reel: 0,
        reelCount: {{ $trendingReels->count() }},
        timer: null,
    
        init() {
            if (this.bannerCount > 1) {
                this.timer = setInterval(() => {
                    this.banner = (this.banner + 1) % this.bannerCount
                }, 600000)
            }
        },
    
        goBanner(index) {
            this.banner = index
    
            if (this.timer) {
                clearInterval(this.timer)
    
                if (this.bannerCount > 1) {
                    this.timer = setInterval(() => {
                        this.banner = (this.banner + 1) % this.bannerCount
                    }, 6000)
                }
            }
        },
    
        nextReel() {
            if (this.reelCount > 1) {
                this.reel = (this.reel + 1) % this.reelCount
            }
        },
    
        previousReel() {
            if (this.reelCount > 1) {
                this.reel =
                    (this.reel - 1 + this.reelCount) % this.reelCount
            }
        }
    }" x-init="init()" class="w-full">

        {{-- ===================================================== --}}
        {{-- MAIN HERO CARD                                       --}}
        {{-- ===================================================== --}}

        <div class="theme-card overflow-hidden rounded-3xl border border-slate-800/80 shadow-2xl">

            {{-- ================================================= --}}
            {{-- BANNER SLIDER                                    --}}
            {{-- ================================================= --}}

            @if ($slides->isNotEmpty())

                <div class="relative h-[360px] overflow-hidden sm:h-[360px] lg:h-[360px]">

                    @foreach ($slides as $k => $slide)
                        <div x-show="banner === {{ $k }}"
                            x-transition:enter="transition-opacity duration-500 ease-out"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity duration-300 ease-in"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute inset-0">

                            {{-- ================================= --}}
                            {{-- BANNER IMAGE                       --}}
                            {{-- ================================= --}}

                            @if ($slide->image)
                                <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}"
                                    class="absolute inset-0 h-full w-full object-cover object-center">

                                {{-- Dark overlay so text remains readable --}}
                                <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/45 to-black/10">
                                </div>

                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/10">
                                </div>
                            @else
                                <div class="absolute inset-0 theme-inner"></div>
                            @endif


                            {{-- ================================= --}}
                            {{-- BANNER CONTENT                     --}}
                            {{-- ================================= --}}

                            <div
                                class="relative z-10 flex h-full max-w-2xl flex-col justify-center px-6 py-8 sm:px-10 lg:px-14">

                                {{-- Tag --}}
                                @if ($slide->tag)
                                    <span
                                        class="theme-soft-bg theme-text mb-4 inline-flex w-fit rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.18em]">
                                        {{ $slide->tag }}
                                    </span>
                                @endif


                                {{-- Title --}}
                                <h1
                                    class="max-w-xl text-3xl font-black leading-[1.08] text-white sm:text-4xl lg:text-5xl">
                                    {{ $slide->title }}

                                    @if ($slide->accent)
                                        <span class="theme-text block">
                                            {{ $slide->accent }}
                                        </span>
                                    @endif

                                </h1>


                                {{-- Description --}}
                                @if ($slide->sub)
                                    <p class="mt-4 max-w-lg text-sm leading-6 text-slate-300 sm:text-base">
                                        {{ $slide->sub }}
                                    </p>
                                @endif


                                {{-- Buttons --}}
                                <div class="mt-6 flex flex-wrap gap-3">

                                    @if ($slide->cta_text && $slide->cta_route)
                                        <a href="{{ route($slide->cta_route) }}"
                                            class="theme-btn inline-flex items-center gap-2 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-wide shadow-lg transition hover:brightness-110">
                                            {{ $slide->cta_text }}

                                            <x-heroicon-o-arrow-right class="size-4" />
                                        </a>
                                    @endif


                                    <a href="{{ route('reels.index') }}"
                                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-black/30 px-5 py-3 text-xs font-bold text-white backdrop-blur-sm transition hover:bg-white/10">
                                        <x-heroicon-o-play class="size-4" />

                                        Watch reels
                                    </a>

                                </div>

                            </div>

                        </div>
                    @endforeach


                    {{-- ========================================= --}}
                    {{-- BANNER NAVIGATION                         --}}
                    {{-- ========================================= --}}

                    @if ($slides->count() > 1)

                        <div class="absolute bottom-5 left-6 z-30 flex items-center gap-2 sm:left-10 lg:left-14">

                            @foreach ($slides as $k => $slide)
                                <button type="button" @click="goBanner({{ $k }})"
                                    aria-label="Go to slide {{ $k + 1 }}"
                                    :class="banner === {{ $k }} ?
                                        'w-7 bg-(--accent-primary)' :
                                        'w-2 bg-white/40 hover:bg-white/70'"
                                    class="h-2 rounded-full transition-all duration-300"></button>
                            @endforeach

                        </div>

                    @endif


                    {{-- Slide counter --}}

                    @if ($slides->count() > 1)
                        <div
                            class="absolute bottom-5 right-6 z-30 rounded-full border border-white/10 bg-black/40 px-3 py-1.5 text-[10px] font-bold text-white backdrop-blur-sm">
                            <span x-text="banner + 1"></span>
                            /
                            {{ $slides->count() }}
                        </div>
                    @endif

                </div>
            @else
                {{-- No banner --}}
                <div class="flex h-[360px] items-center justify-center theme-inner sm:h-[400px]">

                    <div class="text-center text-slate-600">

                        <x-heroicon-o-photo class="mx-auto size-16" />

                        <p class="mt-3 text-sm font-semibold">
                            No active banners available
                        </p>

                    </div>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- REEL PROMOTION                                   --}}
            {{-- ================================================= --}}

            @if ($trendingReels->isNotEmpty())

                @php
                    $heroReel = $trendingReels->first();
                @endphp

                <div class="border-t border-slate-800/80 bg-black/10 p-4 sm:p-5 lg:p-6">

                    {{-- Reel heading --}}

                    <div class="mb-4 flex items-center justify-between gap-3">

                        <div>

                            <p class="theme-text text-[10px] font-black uppercase tracking-[0.2em]">
                                ReelMarket TV
                            </p>

                            <h2 class="mt-1 text-lg font-black text-white sm:text-xl">
                                Shop through reels
                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Watch creators. Discover products. Shop what you love.
                            </p>

                        </div>


                        {{-- Reel navigation --}}

                        @if ($trendingReels->count() > 1)
                            <div class="flex items-center gap-2">

                                <button type="button" @click="previousReel()" aria-label="Previous reel"
                                    class="theme-inner flex size-9 items-center justify-center rounded-xl border border-slate-800 text-slate-300 transition hover:border-slate-600 hover:text-white">
                                    <x-heroicon-o-chevron-left class="size-4" />
                                </button>

                                <button type="button" @click="nextReel()" aria-label="Next reel"
                                    class="theme-inner flex size-9 items-center justify-center rounded-xl border border-slate-800 text-slate-300 transition hover:border-slate-600 hover:text-white">
                                    <x-heroicon-o-chevron-right class="size-4" />
                                </button>

                            </div>
                        @endif

                    </div>


                    {{-- ========================================= --}}
                    {{-- REEL SLIDER                               --}}
                    {{-- ========================================= --}}

                    <div class="relative overflow-hidden">

                        @foreach ($trendingReels as $rIndex => $reel)
                            @php
                                $reelUser = $reel->user;
                                $reelProfile = $reelUser?->profile;
                                $reelProduct = $reel->product;
                                $reelProductImage = $reelProduct?->images?->first()?->image;

                                $reelPrice = $reelProduct
                                    ? (float) ($reelProduct->sale_price ?: $reelProduct->price)
                                    : null;
                            @endphp

                            <div x-show="reel === {{ $rIndex }}"
                                x-transition:enter="transition-opacity duration-300"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                class="grid gap-4 md:grid-cols-5">

                                {{-- ================================= --}}
                                {{-- REEL INFO                          --}}
                                {{-- ================================= --}}

                                <div
                                    class="theme-inner flex min-w-0 flex-col justify-between rounded-2xl border border-slate-800/80 p-5 md:col-span-3">

                                    <div>

                                        {{-- Creator --}}

                                        <div class="flex items-center gap-3">

                                            <div
                                                class="theme-soft-bg theme-text flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-700 text-xs font-black uppercase">

                                                @if ($reelProfile?->profile_picture)
                                                    <img src="{{ asset('storage/' . $reelProfile->profile_picture) }}"
                                                        alt="{{ $reelUser?->name }}"
                                                        class="h-full w-full object-cover">
                                                @else
                                                    {{ Str::substr($reelUser?->name ?? 'U', 0, 1) }}
                                                @endif

                                            </div>


                                            <div class="min-w-0">

                                                <p class="truncate text-sm font-black text-white">
                                                    {{ $reelProfile?->username ?? $reelUser?->name }}
                                                </p>

                                                <p class="text-[11px] text-slate-500">
                                                    Featured creator
                                                </p>

                                            </div>

                                        </div>


                                        {{-- Reel title --}}

                                        <h3
                                            class="mt-5 line-clamp-2 text-xl font-black leading-tight text-white sm:text-2xl">
                                            {{ $reel->caption ?? 'Discover something worth watching' }}
                                        </h3>


                                        {{-- Product --}}

                                        @if ($reelProduct)
                                            <div
                                                class="theme-card mt-5 flex items-center justify-between gap-3 rounded-2xl border border-slate-800/80 p-3">

                                                <div class="flex min-w-0 items-center gap-3">

                                                    <div
                                                        class="theme-inner flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-xl">

                                                        @if ($reelProductImage)
                                                            <img src="{{ asset('storage/' . $reelProductImage) }}"
                                                                alt="{{ $reelProduct->name }}"
                                                                class="h-full w-full object-cover">
                                                        @else
                                                            <x-heroicon-o-shopping-bag class="size-5 text-slate-500" />
                                                        @endif

                                                    </div>


                                                    <div class="min-w-0">

                                                        <p class="truncate text-xs font-bold text-white">
                                                            {{ $reelProduct->name }}
                                                        </p>

                                                        @if ($reelPrice !== null)
                                                            <p class="mt-0.5 text-sm font-black theme-text">
                                                                ₹{{ number_format($reelPrice, 0) }}
                                                            </p>
                                                        @endif

                                                    </div>

                                                </div>


                                                <a href="{{ route('product.detail', $reelProduct->slug ?? $reelProduct->id) }}"
                                                    class="theme-btn shrink-0 rounded-xl px-4 py-2.5 text-[11px] font-black uppercase tracking-wide">
                                                    Shop this reel
                                                </a>

                                            </div>
                                        @endif

                                    </div>


                                    {{-- Reel buttons --}}

                                    <div class="mt-5 flex flex-wrap gap-2">

                                        <a href="{{ route('reels.index', ['reel' => $reel->id]) }}"
                                            class="theme-btn inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-black">
                                            <x-heroicon-o-play class="size-4" />
                                            Watch reel
                                        </a>

                                        <a href="{{ route('reels.index') }}"
                                            class="inline-flex items-center gap-2 rounded-xl border border-slate-700 px-4 py-2.5 text-xs font-bold text-slate-200 transition hover:border-slate-500 hover:text-white">
                                            All reels
                                        </a>

                                    </div>

                                </div>


                                {{-- ================================= --}}
                                {{-- REEL PREVIEW                       --}}
                                {{-- ================================= --}}

                                <a href="{{ route('reels.index', ['reel' => $reel->id]) }}"
                                    class="group relative min-h-[280px] overflow-hidden rounded-2xl border border-slate-800 bg-slate-950 md:col-span-2">

                                    @if ($reel->thumbnail)
                                        <img src="{{ asset('storage/' . $reel->thumbnail) }}"
                                            alt="Reel by {{ $reelProfile?->username ?? $reelUser?->name }}"
                                            class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center bg-slate-950">
                                            <x-heroicon-o-video-camera class="size-12 text-slate-700" />
                                        </div>
                                    @endif


                                    {{-- Preview overlay --}}

                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-black/20">
                                    </div>


                                    {{-- Play button --}}

                                    <span
                                        class="absolute left-1/2 top-1/2 flex size-14 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-black shadow-xl transition group-hover:scale-110">
                                        <x-heroicon-s-play class="ml-0.5 size-6" />
                                    </span>


                                    {{-- Creator name --}}

                                    <div class="absolute inset-x-0 bottom-0 p-4">

                                        <p class="truncate text-xs font-black text-white">
                                            {{ $reelProfile?->username ?? $reelUser?->name }}
                                        </p>

                                        <p
                                            class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-white/60">
                                            Watch & shop
                                        </p>

                                    </div>

                                </a>

                            </div>
                        @endforeach

                    </div>


                    {{-- Reel dots --}}

                    @if ($trendingReels->count() > 1)

                        <div class="mt-4 flex items-center justify-center gap-1.5">

                            @foreach ($trendingReels as $rIndex => $reel)
                                <button type="button" @click="reel = {{ $rIndex }}"
                                    :class="reel === {{ $rIndex }} ?
                                        'w-6 bg-(--accent-primary)' :
                                        'w-1.5 bg-slate-700'"
                                    class="h-1.5 rounded-full transition-all duration-300"
                                    aria-label="Reel {{ $rIndex + 1 }}"></button>
                            @endforeach

                        </div>

                    @endif

                </div>
            @else
                {{-- ================================================ --}}
                {{-- NO REELS: SIMPLE SOCIAL PROMOTION                 --}}
                {{-- ================================================ --}}

                <div
                    class="flex flex-col gap-4 border-t border-slate-800/80 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">

                    <div class="flex items-center gap-4">

                        <div
                            class="theme-soft-bg theme-text flex size-12 shrink-0 items-center justify-center rounded-full">
                            <x-heroicon-o-users class="size-6" />
                        </div>

                        <div>

                            <p class="text-sm font-black text-white">
                                Join the community
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Follow creators, watch stories and discover products.
                            </p>

                        </div>

                    </div>


                    <a href="{{ route('reels.index') }}"
                        class="theme-btn inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-xs font-black">
                        Explore reels

                        <x-heroicon-o-arrow-right class="size-4" />
                    </a>

                </div>

            @endif

        </div>

    </section>
    {{-- ================= TRUST STRIP ================= --}}
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ($trust as $item)
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
    @if ($categories->isNotEmpty())
        <section class="theme-card rounded-2xl px-3 py-3">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-black text-white">Shop by category</h2>
                <a href="{{ route('shop.index') }}" class="theme-text text-xs font-bold hover:underline">View all</a>
            </div>
            <div class="no-scrollbar theme-card flex items-center gap-3 rounded-2xl overflow-x-auto">
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index', ['selectedCategory' => $category->slug ?? $category->id]) }}"
                        wire:key="home-cat-{{ $category->id }}"
                        class="group flex w-20 shrink-0 flex-col items-center gap-2 text-center">
                        <span
                            class="theme-card flex size-16 items-center justify-center overflow-hidden rounded-2xl border border-slate-800 text-slate-300 transition group-hover:border-(--accent-primary) group-hover:text-(--accent-text)">
                            @if ($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <x-heroicon-o-tag class="size-6" />
                            @endif
                        </span>
                        <span
                            class="w-full truncate text-[11px] font-semibold text-slate-300 group-hover:text-white">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ================= MAIN GRID ================= --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 theme-card rounded-2xl px-1 py-1">

        <div class="min-w-0 space-y-8 lg:col-span-8">

            {{-- ---------- Trending products ---------- --}}
            @if ($trendingProducts->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-lg font-black text-white">
                            <x-heroicon-o-fire class="theme-text size-5" />
                            Trending products
                        </h2>
                        <a href="{{ route('shop.index') }}" class="theme-text text-xs font-bold hover:underline">View
                            all</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                        @foreach ($trendingProducts as $product)
                            @php
                                $img = $product->images->first()?->image;
                                $hasSale =
                                    (float) $product->sale_price > 0 &&
                                    (float) $product->sale_price < (float) $product->price;
                                $off = $hasSale
                                    ? (int) round((1 - (float) $product->sale_price / (float) $product->price) * 100)
                                    : 0;
                                $startingPrice = $product->variants->isNotEmpty()
                                    ? $product->variants
                                        ->map(fn($variant) => (float) ($variant->price ?? $product->final_price))
                                        ->min()
                                    : (float) $product->final_price;
                                $inStock = $product->is_in_stock;
                            @endphp

                            <article wire:key="home-product-{{ $product->id }}"
                                class="group theme-card flex flex-col overflow-hidden rounded-2xl border border-slate-800/80 transition hover:-translate-y-0.5 hover:border-slate-700">
                                <a href="{{ route('product.detail', $product->slug ?? $product->id) }}"
                                    class="relative block aspect-square overflow-hidden bg-slate-950">
                                    @if ($img)
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}"
                                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-600">
                                            <x-heroicon-o-photo class="size-8" />
                                        </div>
                                    @endif

                                    @if ($hasSale)
                                        <span
                                            class="absolute left-2 top-2 rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-black text-white">{{ $off }}%
                                            off</span>
                                    @elseif($product->featured)
                                        <span
                                            class="theme-btn absolute left-2 top-2 rounded-full px-2 py-0.5 text-[10px] font-black">Featured</span>
                                    @endif

                                    @if (!$inStock)
                                        <span
                                            class="absolute right-2 top-2 rounded-full border border-slate-700 bg-slate-950/85 px-2 py-0.5 text-[10px] font-bold text-slate-300">Sold
                                            out</span>
                                    @endif
                                </a>

                                <div class="flex flex-1 flex-col gap-1.5 p-3">
                                    <span
                                        class="theme-text truncate text-[10px] font-black uppercase tracking-[0.16em]">
                                        {{ $product->brand?->name ?? ($product->category?->name ?? 'Store item') }}
                                    </span>
                                    <a href="{{ route('product.detail', $product->slug ?? $product->id) }}"
                                        class="line-clamp-2 text-xs font-bold text-white hover:text-(--accent-text)">
                                        {{ $product->name }}
                                    </a>

                                    <div class="mt-auto pt-1">
                                        <p class="text-sm font-black text-white">
                                            {{ $product->variants->isNotEmpty() ? 'From ' : '' }}₹{{ number_format($startingPrice, 0) }}
                                            @if ($hasSale)
                                                <span
                                                    class="ml-1 text-[11px] font-medium text-slate-500 line-through">₹{{ number_format((float) $product->price, 0) }}</span>
                                            @endif
                                        </p>
                                    </div>

                                    <a href="{{ route('product.detail', $product->slug ?? $product->id) }}"
                                        class="theme-btn mt-1 block rounded-xl py-2 text-center text-[11px] font-black uppercase tracking-wide">
                                        {{ $product->variants->isNotEmpty() ? 'Choose options' : 'View product' }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ---------- Trending reels ---------- --}}
            @if ($trendingReels->isNotEmpty())
                <section>
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-lg font-black text-white">
                            <x-heroicon-o-play-circle class="theme-text size-5" />
                            Trending reels
                        </h2>
                        <a href="{{ route('reels.index') }}"
                            class="theme-text text-xs font-bold hover:underline">Watch all</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                        @foreach ($trendingReels as $reel)
                            <a href="{{ route('reels.index') }}" wire:key="home-reel-{{ $reel->id }}"
                                class="group theme-card relative block aspect-[3/4] overflow-hidden rounded-2xl border border-slate-800/80">
                                @if ($reel->thumbnail)
                                    <img src="{{ asset('storage/' . $reel->thumbnail) }}" alt="Reel"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @endif
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <x-heroicon-s-play-circle class="size-11 text-white/85" />
                                </span>
                                <span class="absolute inset-x-0 bottom-0 space-y-0.5 bg-black/60 px-3 py-2">
                                    <span
                                        class="block truncate text-[11px] font-bold text-white">{{ $reel->user?->profile?->username ?? $reel->user?->name }}</span>
                                    @if ($reel->product)
                                        <span
                                            class="theme-text block truncate text-[10px] font-semibold">{{ $reel->product->name }}</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>

        {{-- ================= RIGHT SIDEBAR ================= --}}
        <aside class="space-y-5 lg:col-span-4">
            <div class="space-y-5 lg:sticky lg:top-36">

                @if ($dealProduct)
                    @php
                        $dealImg = $dealProduct->images->first()?->image;
                        $dealOff = (int) round(
                            (1 - (float) $dealProduct->sale_price / (float) $dealProduct->price) * 100,
                        );
                    @endphp
                    <div class="theme-card theme-border overflow-hidden rounded-2xl border p-4 shadow-xl">
                        <div
                            class="theme-text flex items-center justify-between text-xs font-black uppercase tracking-[0.16em]">
                            <span class="flex items-center gap-1.5">
                                <x-heroicon-o-fire class="size-4" />
                                Deal of the day
                            </span>
                            <span
                                class="rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-black text-white">{{ $dealOff }}%
                                off</span>
                        </div>

                        <a href="{{ route('product.detail', $dealProduct->slug ?? $dealProduct->id) }}"
                            class="theme-inner mt-3 block aspect-[4/3] overflow-hidden rounded-xl">
                            @if ($dealImg)
                                <img src="{{ asset('storage/' . $dealImg) }}" alt="{{ $dealProduct->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-600">
                                    <x-heroicon-o-photo class="size-10" />
                                </div>
                            @endif
                        </a>

                        <p class="mt-3 line-clamp-2 text-sm font-black text-white">{{ $dealProduct->name }}</p>
                        <p class="mt-1 text-lg font-black text-white">
                            ₹{{ number_format($dealProduct->final_price, 0) }}
                            <span
                                class="ml-1 text-xs font-medium text-slate-500 line-through">₹{{ number_format((float) $dealProduct->price, 0) }}</span>
                        </p>

                        <div wire:ignore x-data="{
                            end: {{ $dealEndsAtMs }},
                            h: '00',
                            m: '00',
                            s: '00',
                            tick() {
                                const d = Math.max(0, Math.floor((this.end - Date.now()) / 1000));
                                this.h = String(Math.floor(d / 3600)).padStart(2, '0');
                                this.m = String(Math.floor((d % 3600) / 60)).padStart(2, '0');
                                this.s = String(d % 60).padStart(2, '0');
                            }
                        }" x-init="tick();
                        setInterval(() => tick(), 1000)"
                            class="mt-3 flex items-center gap-2 text-xs">
                            <x-heroicon-o-clock class="size-4 text-slate-500" />
                            <span class="text-slate-500">Ends in</span>
                            <span class="theme-inner rounded-lg px-2 py-1 font-black tabular-nums text-white"
                                x-text="h"></span>
                            <span class="theme-inner rounded-lg px-2 py-1 font-black tabular-nums text-white"
                                x-text="m"></span>
                            <span class="theme-inner theme-text rounded-lg px-2 py-1 font-black tabular-nums"
                                x-text="s"></span>
                        </div>

                        <a href="{{ route('product.detail', $dealProduct->slug ?? $dealProduct->id) }}"
                            class="theme-btn mt-4 block rounded-xl py-2.5 text-center text-xs font-black uppercase tracking-wide">
                            Grab deal
                        </a>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    <livewire:report-content />
    <livewire:story.story-viewer />
</div>
