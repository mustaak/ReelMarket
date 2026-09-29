@php
    $theme = session('user_theme', 'amber');
    $bgTheme = session('user_bg', 'midnight');

    $accentMap = [
        'amber'   => ['primary' => '#f59e0b', 'rgb' => '245, 158, 11',  'text' => '#fbbf24'],
        'rose'    => ['primary' => '#f43f5e', 'rgb' => '244, 63, 94',   'text' => '#fb7185'],
        'emerald' => ['primary' => '#10b981', 'rgb' => '16, 185, 129',  'text' => '#34d399'],
        'indigo'  => ['primary' => '#6366f1', 'rgb' => '99, 102, 241',  'text' => '#818cf8'],
        'cyan'    => ['primary' => '#06b6d4', 'rgb' => '6, 182, 212',   'text' => '#22d3ee'],
        'purple'  => ['primary' => '#a855f7', 'rgb' => '168, 85, 247',  'text' => '#c084fc'],
        'orange'  => ['primary' => '#f97316', 'rgb' => '249, 115, 22',  'text' => '#fb923c'],
    ][$theme] ?? ['primary' => '#f59e0b', 'rgb' => '245, 158, 11', 'text' => '#fbbf24'];

    $bgMap = [
        'midnight' => ['body' => '#0b0813', 'card' => '#140e26', 'inner' => '#1a1333'],
        'black'    => ['body' => '#050505', 'card' => '#121212', 'inner' => '#1c1c1c'],
        'navy'     => ['body' => '#060a12', 'card' => '#0d1527', 'inner' => '#131d33'],
        'slate'    => ['body' => '#0f172a', 'card' => '#1e293b', 'inner' => '#334155'],
    ][$bgTheme] ?? ['body' => '#0b0813', 'card' => '#140e26', 'inner' => '#1a1333'];

    // Single source of truth for navigation, so the sidebar and the mobile bar can't drift apart
    $navLinks = [
        ['route' => 'home',        'label' => 'Home',     'icon' => 'heroicon-o-home',                   'active' => 'home'],
        ['route' => 'shop.index',  'label' => 'Shop',      'icon' => 'heroicon-o-shopping-bag',           'active' => 'shop.*'],
        ['route' => 'reels.index', 'label' => 'Reels',     'icon' => 'heroicon-o-play-circle',            'active' => 'reels.*'],
        ['route' => 'cart.index',  'label' => 'Cart',      'icon' => 'heroicon-o-shopping-cart',          'active' => 'cart.*'],
    ];
@endphp

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>YourBrand</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style>
        :root {
            --accent-primary: {{ $accentMap['primary'] }};
            --accent-rgb: {{ $accentMap['rgb'] }};
            --accent-text: {{ $accentMap['text'] }};
            --bg-body: {{ $bgMap['body'] }};
            --bg-card: {{ $bgMap['card'] }};
            --bg-inner: {{ $bgMap['inner'] }};
            box-sizing: border-box;
            padding-top: env(safe-area-inset-top, 0px);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        html { scroll-padding-top: env(safe-area-inset-top, 0px); }
        body { background-color: var(--bg-body) !important; }
        .theme-card { background-color: var(--bg-card) !important; }
        .theme-inner { background-color: var(--bg-inner) !important; }
        .theme-soft-bg { background-color: color-mix(in srgb, var(--accent-primary) 15%, transparent) !important; }
        .theme-btn { background-color: var(--accent-primary) !important; color: #000000 !important; }
        .theme-btn:hover { filter: brightness(1.1); }
        .theme-text { color: var(--accent-text) !important; }
        .theme-border { border-color: var(--accent-primary) !important; }
    </style>
</head>
<body class="min-h-dvh text-slate-100 antialiased transition-colors duration-300">

    <!-- MOBILE HEADER -->
    <header class="theme-card/95 sticky top-0 z-40 flex items-center justify-between border-b border-slate-800/80 px-4 py-3 backdrop-blur-md md:hidden">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="theme-btn flex size-8 items-center justify-center rounded-xl text-base font-black">Y</span>
            <span class="text-lg font-black tracking-wide text-white">YourBrand</span>
        </a>
        @livewire('theme-switcher')
    </header>

    <!-- DESKTOP HEADER -->
    <header class="theme-card/95 sticky top-0 z-40 hidden border-b border-slate-800/80 backdrop-blur-md md:block">
        <div class="mx-auto flex max-w-6xl items-center gap-6 px-6 py-3 lg:px-8">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
                <span class="theme-btn flex size-9 items-center justify-center rounded-xl text-lg font-black">Y</span>
                <span class="text-xl font-black tracking-wide text-white">YourBrand</span>
            </a>

            <form action="{{ route('shop.index') }}" method="GET" class="relative w-full max-w-md">
                <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-500" />
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Search products, brands..."
                       class="theme-inner w-full rounded-xl border border-slate-800 py-2.5 pl-10 pr-4 text-sm text-white placeholder:text-slate-500 focus:border-(--accent-primary) focus:outline-none">
            </form>

            <div class="ml-auto flex shrink-0 items-center gap-2">
                <button type="button" onclick="Livewire.dispatch('open-cart')"
                        aria-label="Cart"
                        class="relative rounded-xl p-2.5 text-slate-300 transition hover:bg-white/5 hover:text-white">
                    <x-heroicon-o-shopping-cart class="size-6" />
                    <span class="absolute -right-0.5 -top-0.5"><livewire:cart-count /></span>
                </button>

                @livewire('theme-switcher')

                <a href="" aria-label="Profile"
                   class="rounded-xl p-1 transition hover:bg-white/5">
                    <span class="theme-soft-bg theme-text flex size-8 items-center justify-center rounded-full text-sm font-black">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </span>
                </a>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-[1600px] px-4 pb-24 sm:px-6 md:pb-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 py-4 md:grid-cols-12 md:py-6">

            <!-- DESKTOP SIDEBAR -->
            <aside class="hidden md:col-span-3 md:block">
                <div class="theme-card sticky top-20 space-y-5 rounded-2xl border border-slate-800/80 p-5 shadow-xl transition-colors duration-300">
                    <nav class="space-y-1">
                        @foreach ($navLinks as $link)
                            @php $isActive = request()->routeIs($link['active']); @endphp
                            <a href="{{ route($link['route']) }}"
                               @class([
                                   'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition',
                                   'theme-soft-bg theme-text font-bold' => $isActive,
                                   'font-medium text-slate-400 hover:text-white' => ! $isActive,
                               ])>
                                <x-dynamic-component :component="$link['icon']" class="size-5" />
                                <span>{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            <!-- MAIN CONTENT -->
            <main @class([
                'col-span-1 space-y-6',
                'md:col-span-9 lg:col-span-6' => request()->routeIs('home'),
                'md:col-span-9' => ! request()->routeIs('home'),
            ])>
                {{ $slot }}
            </main>

            <!-- RIGHT SIDEBAR (home only) -->
            @if (request()->routeIs('home'))
                <aside class="hidden lg:col-span-3 lg:block">
                    <div class="theme-card sticky top-20 space-y-4 rounded-2xl border border-slate-800/80 p-4 shadow-xl">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Trending products</h3>

                        @forelse ($trendingProducts ?? [] as $product)
                            @php $firstImg = $product->images->first()?->image; @endphp
                            <div wire:key="trending-product-{{ $product->id }}"
                                 class="theme-inner flex items-center justify-between gap-3 rounded-xl border border-slate-800 p-2">
                                <div class="flex min-w-0 items-center gap-2.5">
                                    <div class="theme-soft-bg flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-lg">
                                        @if ($firstImg)
                                            <img src="{{ asset('storage/' . $firstImg) }}" alt="{{ $product->name }}" class="size-full object-cover">
                                        @else
                                            <span class="theme-text text-xs font-bold">{{ strtoupper(substr($product->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="truncate text-xs font-bold text-white">{{ $product->name }}</h4>
                                        <p class="theme-text text-[11px] font-semibold">₹{{ number_format($product->price, 0) }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('product.detail', $product->slug) }}"
                                   class="theme-btn shrink-0 rounded-lg px-2.5 py-1 text-xs font-bold">
                                    View
                                </a>
                            </div>
                        @empty
                            <p class="py-3 text-center text-xs text-slate-500">No trending products available.</p>
                        @endforelse
                    </div>
                </aside>
            @endif
        </div>
    </div>

    <!-- MOBILE BOTTOM NAV -->
    <nav class="theme-card fixed inset-x-0 bottom-0 z-50 border-t border-slate-800/80 pb-[max(0.375rem,env(safe-area-inset-bottom))] pt-1.5 md:hidden">
        <div class="mx-auto flex max-w-md items-stretch justify-around">
            @foreach ($navLinks as $link)
                @php $isActive = request()->routeIs($link['active']); @endphp
                <a href="{{ route($link['route']) }}"
                   aria-label="{{ $link['label'] }}"
                   aria-current="{{ $isActive ? 'page' : 'false' }}"
                   class="flex flex-1 flex-col items-center gap-0.5 rounded-xl px-2 py-1.5 {{ $isActive ? 'theme-text' : 'text-slate-500' }}">
                    <x-dynamic-component :component="$link['icon']" class="size-6" style="{{ $isActive ? 'stroke-width: 2.2' : '' }}" />
                    <span class="text-[10px] leading-none {{ $isActive ? 'font-bold' : 'font-medium' }}">{{ $link['label'] }}</span>
                </a>
            @endforeach

            <button type="button" onclick="Livewire.dispatch('open-cart')"
                    aria-label="Cart"
                    class="flex flex-1 flex-col items-center gap-0.5 rounded-xl px-2 py-1.5 {{ request()->routeIs('cart.index') ? 'theme-text' : 'text-slate-500' }}">
                <span class="relative">
                    <x-heroicon-o-shopping-cart class="size-6" />
                    <span class="absolute -right-2 -top-1.5"><livewire:cart-count /></span>
                </span>
                <span class="text-[10px] font-medium leading-none">Cart</span>
            </button>
        </div>
    </nav>

    <livewire:cart-drawer />

    <script>
        document.addEventListener('livewire:initialized', () => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
            });

            Livewire.on('notify', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;

                Toast.fire({
                    icon: payload.type || 'success',
                    title: payload.message || 'Done!',
                });
            });
        });
    </script>

    @livewireScripts
</body>
</html>