@php
    $theme = session('user_theme', 'amber');
    $bgTheme = session('user_bg', 'midnight');

    $accentMap = [
        'amber'   => ['primary' => '#e6a23c', 'rgb' => '230, 162, 60',  'text' => '#a45b0a', 'dark_text' => '#f4bd63'],
        'rose'    => ['primary' => '#ef476f', 'rgb' => '239, 71, 111',  'text' => '#b4234a', 'dark_text' => '#ff829d'],
        'emerald' => ['primary' => '#28a17b', 'rgb' => '40, 161, 123',  'text' => '#08775b', 'dark_text' => '#5bd0a7'],
        'indigo'  => ['primary' => '#7569d7', 'rgb' => '117, 105, 215', 'text' => '#4b41a8', 'dark_text' => '#aaa1ff'],
        'cyan'    => ['primary' => '#26a6b8', 'rgb' => '38, 166, 184',  'text' => '#0c7486', 'dark_text' => '#63d8e6'],
        'purple'  => ['primary' => '#aa69bd', 'rgb' => '170, 105, 189','text' => '#7d3497', 'dark_text' => '#d79ce8'],
        'orange'  => ['primary' => '#ec8040', 'rgb' => '236, 128, 64', 'text' => '#b84d16', 'dark_text' => '#ffad75'],
    ][$theme] ?? ['primary' => '#e6a23c', 'rgb' => '230, 162, 60', 'text' => '#a45b0a', 'dark_text' => '#f4bd63'];

    $bgMap = [
        'midnight' => ['body' => '#f6f3ec', 'card' => '#ffffff', 'inner' => '#f8f5ef', 'ink' => '#28231d', 'mute' => '#756d62', 'line' => '#e9e3d8', 'brand_ink' => '#251a0d'],
        'black'    => ['body' => '#090909', 'card' => '#111111', 'inner' => '#1a1a1a', 'ink' => '#f5f5f5', 'mute' => '#a1a1aa', 'line' => '#2b2b2b', 'brand_ink' => '#17120b'],
        'gray'     => ['body' => '#18181b', 'card' => '#222225', 'inner' => '#303034', 'ink' => '#f4f4f5', 'mute' => '#b0b0b8', 'line' => '#414147', 'brand_ink' => '#17120b'],
        'navy'     => ['body' => '#f1f5f4', 'card' => '#ffffff', 'inner' => '#eef3f1', 'ink' => '#28231d', 'mute' => '#756d62', 'line' => '#e9e3d8', 'brand_ink' => '#251a0d'],
        'slate'    => ['body' => '#f2f2ef', 'card' => '#ffffff', 'inner' => '#ededea', 'ink' => '#28231d', 'mute' => '#756d62', 'line' => '#e9e3d8', 'brand_ink' => '#251a0d'],
    ][$bgTheme] ?? ['body' => '#f6f3ec', 'card' => '#ffffff', 'inner' => '#f8f5ef', 'ink' => '#28231d', 'mute' => '#756d62', 'line' => '#e9e3d8', 'brand_ink' => '#251a0d'];

    $accentText = in_array($bgTheme, ['black', 'gray'], true)
        ? $accentMap['dark_text']
        : $accentMap['text'];

    // Desktop pill navigation (main pages)
   $navLinks = [
        ['route' => 'home', 'label' => 'Home', 'icon' => 'heroicon-o-home', 'active' => 'home'],
        ['route' => 'shop.index', 'label' => 'Shop', 'icon' => 'heroicon-o-shopping-bag', 'active' => 'shop.*'],
        ['route' => 'reels.index', 'label' => 'Reels', 'icon' => 'heroicon-o-play-circle', 'active' => 'reels.*'],
        ['route' => 'social.index', 'label' => 'Social', 'icon' => 'heroicon-o-users', 'active' => 'social.*'], // ← ADD
    ];
@endphp

<!DOCTYPE html>
<html lang="en" class="editorial" data-theme="{{ $bgTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ReelMarket</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style>
        :root {
            --accent-primary: {{ $accentMap['primary'] }};
            --accent-rgb: {{ $accentMap['rgb'] }};
            --accent-text: {{ $accentText }};
            --bg-body: {{ $bgMap['body'] }};
            --bg-card: {{ $bgMap['card'] }};
            --bg-inner: {{ $bgMap['inner'] }};
            --color-ink: {{ $bgMap['ink'] }};
            --color-mute: {{ $bgMap['mute'] }};
            --color-line: {{ $bgMap['line'] }};
            --color-brand-ink: {{ $bgMap['brand_ink'] }};
            color-scheme: {{ in_array($bgTheme, ['black', 'gray'], true) ? 'dark' : 'light' }};
            box-sizing: border-box;
            padding-top: env(safe-area-inset-top, 0px);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        html { scroll-padding-top: env(safe-area-inset-top, 0px); }
        [x-cloak] { display: none !important; }
        body { background-color: var(--bg-body) !important; }
        .theme-card { background-color: var(--bg-card) !important; }
        .theme-inner { background-color: var(--bg-inner) !important; }
        .theme-soft-bg { background-color: color-mix(in srgb, var(--accent-primary) 15%, transparent) !important; }
        .theme-btn { background-color: var(--accent-primary) !important; color: #000000 !important; }
        .theme-btn:hover { filter: brightness(1.1); }
        .theme-text { color: var(--accent-text) !important; }
        .theme-border { border-color: var(--accent-primary) !important; }
        .theme-card, .theme-inner { border-color: var(--color-line) !important; }
        .theme-card { box-shadow: 0 12px 32px rgb(52 39 22 / 5%); }
        .hdr-icon { transition: border-color .15s, color .15s; }
        .hdr-icon:hover { border-color: var(--accent-primary); color: var(--accent-text); }
    </style>
</head>
<body class="editorial-app min-h-dvh text-ink antialiased transition-colors duration-300">

    <!-- MOBILE HEADER (unchanged) -->
    <header class="editorial-header theme-card/95 sticky top-0 z-40 border-b border-slate-800/80 px-4 py-3 backdrop-blur-md md:hidden">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="editorial-brand-mark flex size-8 items-center justify-center rounded-xl text-base font-black text-ink">
                    R
                </span>
                <span class="font-display text-lg font-bold tracking-tight text-ink">
                    ReelMarket
                </span>
            </a>
            <!-- Right Side -->
            <div class="flex items-center gap-2">
                @livewire('theme-switcher')
                @auth
                    <!-- Profile -->
                    <a href="{{ route('profile') }}"
                    aria-label="Profile"
                    class="rounded-xl p-1 transition hover:bg-white/5">
                        <span class="theme-soft-bg theme-text flex size-9 items-center justify-center rounded-full text-sm font-black">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                        </span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                    class="rounded-xl border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- DESKTOP HEADER (sidebar links moved here) -->
    <header class="editorial-header theme-card sticky top-0 z-40 hidden border-b border-slate-800/80 md:block">
        <div class="mx-auto max-w-[1600px] px-6 lg:px-8">

            <!-- Row 1: logo, search, actions -->
            <div class="flex items-center gap-4 pb-2.5 pt-3.5">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
                    <span class="editorial-brand-mark flex size-9 items-center justify-center rounded-xl text-lg font-black text-ink">R</span>
                    <span class="font-display text-xl font-bold tracking-tight text-ink">ReelMarket</span>
                </a>

                <form action="{{ route('shop.index') }}" method="GET" class="flex-1">
                    <div class="theme-inner flex h-10 w-full items-center gap-2 rounded-2xl border border-slate-800 pl-4 pr-1.5 focus-within:border-(--accent-primary)">
                        <x-heroicon-o-magnifying-glass class="size-4 shrink-0 text-slate-500" />
                        <input type="search" name="q" value="{{ request('q') }}"
                               placeholder="Search products, brands..."
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-0">
                        <button type="submit" class="theme-btn rounded-xl px-4 py-1.5 text-xs font-black">Search</button>
                    </div>
                </form>

                <div class="flex shrink-0 items-center gap-2">
                    @auth
                        <!-- Activity -->
                        <a href="{{ route('notifications.index') }}" aria-label="Activity"
                           @class([
                               'hdr-icon theme-inner relative flex size-10 items-center justify-center rounded-xl border',
                               'theme-border theme-text' => request()->routeIs('notifications.*'),
                               'border-slate-800 text-slate-300' => ! request()->routeIs('notifications.*'),
                           ])>
                            <x-heroicon-o-bell class="size-5" />
                            <livewire:unread-notification-count />
                        </a>

                        <!-- Messages -->
                        <a href="{{ route('messages.index') }}" aria-label="Messages"
                           @class([
                               'hdr-icon theme-inner relative flex size-10 items-center justify-center rounded-xl border',
                               'theme-border theme-text' => request()->routeIs('messages.*'),
                               'border-slate-800 text-slate-300' => ! request()->routeIs('messages.*'),
                           ])>
                            <x-heroicon-o-chat-bubble-left-right class="size-5" />
                            <livewire:unread-message-count />
                        </a>
                    @endauth

                    <!-- Cart -->
                    <button type="button" onclick="Livewire.dispatch('open-cart')" aria-label="Cart"
                            class="hdr-icon theme-inner relative flex size-10 items-center justify-center rounded-xl border border-slate-800 text-slate-300">
                        <x-heroicon-o-shopping-cart class="size-5" />
                        <span class="absolute -right-1 -top-1"><livewire:cart-count /></span>
                    </button>

                    @auth
                        <!-- Profile menu -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                            <button type="button" @click="open = !open" aria-label="Account menu" class="relative block">
                                <span class="theme-soft-bg theme-text theme-border flex size-10 items-center justify-center rounded-full border-[1.5px] text-sm font-black">
                                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                                </span>
                                <span class="absolute -bottom-0.5 -right-0.5 size-2.5 rounded-full border-2 border-[var(--bg-card)] bg-green-500"></span>
                            </button>

                            <div x-show="open" x-cloak x-transition.origin.top.right
                                 class="theme-inner absolute right-0 top-full z-50 mt-2 w-52 rounded-2xl border border-slate-700/80 p-1.5 shadow-2xl">
                                <a href="{{ route('profile') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-200 hover:bg-white/5">
                                    <x-heroicon-o-user class="size-5" /> My profile
                                </a>
                                <a href="{{ route('saved.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-200 hover:bg-white/5">
                                    <x-heroicon-o-bookmark class="size-5" /> Saved posts
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-rose-400 hover:bg-white/5">
                                        <x-heroicon-o-arrow-right-on-rectangle class="size-5" /> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl border border-slate-700 px-3.5 py-2.5 text-xs font-semibold text-slate-200 transition hover:border-slate-500 hover:text-white">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="theme-btn rounded-xl px-3.5 py-2.5 text-xs font-black">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Row 2: pill navigation + theme switcher -->
            <div class="flex items-center justify-between pb-3.5 pt-0.5">
                <nav class="theme-inner inline-flex items-center gap-0.5 rounded-2xl border border-slate-800 p-1">
                    @foreach ($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           @class([
                               'flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold transition',
                               'theme-btn' => request()->routeIs($link['active']),
                               'text-slate-400 hover:text-white' => ! request()->routeIs($link['active']),
                           ])>
                            <x-dynamic-component :component="$link['icon']" class="size-[18px]" />
                            <span>{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                @livewire('theme-switcher')
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-[1600px] px-4 pb-24 sm:px-6 md:pb-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 py-4 md:grid-cols-12 md:py-6">

            <!-- MAIN CONTENT -->
            <main class="editorial-content col-span-1 space-y-6 md:col-span-12">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- MOBILE BOTTOM NAV (unchanged) -->
    <nav class="editorial-bottom-nav theme-card fixed inset-x-0 bottom-0 z-50 border-t border-slate-800/80 pb-[max(0.375rem,env(safe-area-inset-bottom))] pt-1.5 md:hidden">
        <div class="mx-auto flex max-w-md items-center">
            <!-- Primary 4 -->
            <div class="flex flex-1 items-stretch justify-around">
                <!-- Home -->
                <a href="{{ route('home') }}"
                aria-label="Home"
                class="flex flex-1 flex-col items-center gap-0.5 rounded-xl px-2 py-1.5 {{ request()->routeIs('home') ? 'theme-text' : 'text-slate-500' }}">
                    <x-heroicon-o-home class="size-6" />
                    <span class="text-[10px] leading-none {{ request()->routeIs('home') ? 'font-bold' : 'font-medium' }}">
                        Home
                    </span>
                </a>
                <!-- Social -->
                <a href="{{ route('social.index') }}" aria-label="Social"
                    class="flex flex-1 flex-col items-center gap-0.5 rounded-xl px-2 py-1.5 {{ request()->routeIs('social.*') ? 'theme-text' : 'text-slate-500' }}">
                    <x-heroicon-o-users class="size-6" />
                    <span class="text-[10px] leading-none {{ request()->routeIs('social.*') ? 'font-bold' : 'font-medium' }}">
                        Social
                    </span>
                </a>
                <!-- Shop -->
                <a href="{{ route('shop.index') }}"
                aria-label="Shop"
                class="flex flex-1 flex-col items-center gap-0.5 rounded-xl px-2 py-1.5 {{ request()->routeIs('shop.*') ? 'theme-text' : 'text-slate-500' }}">
                    <x-heroicon-o-shopping-bag class="size-6" />
                    <span class="text-[10px] leading-none {{ request()->routeIs('shop.*') ? 'font-bold' : 'font-medium' }}">
                        Shop
                    </span>
                </a>
                <!-- Reels -->
                <a href="{{ route('reels.index') }}"
                aria-label="Reels"
                class="flex flex-1 flex-col items-center gap-0.5 rounded-xl px-2 py-1.5 {{ request()->routeIs('reels.*') ? 'theme-text' : 'text-slate-500' }}">
                    <x-heroicon-o-play-circle class="size-6" />
                    <span class="text-[10px] leading-none {{ request()->routeIs('reels.*') ? 'font-bold' : 'font-medium' }}">
                        Reels
                    </span>
                </a>
                <!-- Cart -->
                <button type="button"
                        onclick="Livewire.dispatch('open-cart')"
                        aria-label="Cart"
                        class="flex flex-1 flex-col items-center gap-0.5 rounded-xl px-2 py-1.5 {{ request()->routeIs('cart.index') ? 'theme-text' : 'text-slate-500' }}">
                    <span class="relative">
                        <x-heroicon-o-shopping-cart class="size-6" />
                        <span class="absolute -right-2 -top-1.5">
                            <livewire:cart-count />
                        </span>
                    </span>
                    <span class="text-[10px] font-medium leading-none">
                        Cart
                    </span>
                </button>
            </div>
            <!-- More / Slider -->
            <div class="relative shrink-0">
                <details class="group relative">
                    <summary
                        class="flex cursor-pointer list-none flex-col items-center gap-0.5 rounded-xl px-3 py-1.5 text-slate-500 transition hover:text-white">
                        <x-heroicon-o-ellipsis-horizontal-circle class="size-6" />
                        <span class="text-[10px] font-medium leading-none">
                            More
                        </span>
                    </summary>
                    <!-- Slider -->
                    <div class="theme-card absolute bottom-full right-0 mb-2 w-[300px] rounded-2xl border border-slate-800/80 p-2 shadow-2xl">

                        <div class="flex gap-2 overflow-x-auto no-scrollbar snap-x snap-mandatory">

                            @auth

                                <!-- Activity -->
                                <a href="{{ route('notifications.index') }}"
                                class="theme-inner flex min-w-[90px] snap-start flex-col items-center justify-center gap-1 rounded-xl border border-slate-800 px-3 py-3 text-slate-300 transition hover:text-white">

                                    <span class="relative">
                                        <x-heroicon-o-bell class="size-6" />

                                        <livewire:unread-notification-count />
                                    </span>

                                    <span class="text-[10px] font-semibold">
                                        Activity
                                    </span>
                                </a>


                                <!-- Messages -->
                                <a href="{{ route('messages.index') }}"
                                class="theme-inner flex min-w-[90px] snap-start flex-col items-center justify-center gap-1 rounded-xl border border-slate-800 px-3 py-3 text-slate-300 transition hover:text-white">

                                    <span class="relative">
                                        <x-heroicon-o-chat-bubble-left-right class="size-6" />

                                        <livewire:unread-message-count />
                                    </span>

                                    <span class="text-[10px] font-semibold">
                                        Messages
                                    </span>
                                </a>


                                <!-- Profile -->
                                <a href="{{ route('profile') }}"
                                class="theme-inner flex min-w-[90px] snap-start flex-col items-center justify-center gap-1 rounded-xl border border-slate-800 px-3 py-3 text-slate-300 transition hover:text-white">

                                    <span class="theme-soft-bg theme-text flex size-6 items-center justify-center rounded-full text-[10px] font-black">
                                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                                    </span>

                                    <span class="text-[10px] font-semibold">
                                        Profile
                                    </span>
                                </a>


                                <!-- Logout -->
                                <form method="POST"
                                    action="{{ route('logout') }}"
                                    class="min-w-[90px] snap-start">

                                    @csrf

                                    <button type="submit"
                                            class="theme-inner flex w-full flex-col items-center justify-center gap-1 rounded-xl border border-slate-800 px-3 py-3 text-slate-300 transition hover:text-white">

                                        <x-heroicon-o-arrow-right-on-rectangle class="size-6" />

                                        <span class="text-[10px] font-semibold">
                                            Logout
                                        </span>
                                    </button>
                                </form>

                            @else

                                <a href="{{ route('login') }}"
                                class="theme-inner flex min-w-[90px] snap-start flex-col items-center justify-center gap-1 rounded-xl border border-slate-800 px-3 py-3 text-slate-300">

                                    <x-heroicon-o-arrow-right-end-on-rectangle class="size-6" />

                                    <span class="text-[10px] font-semibold">
                                        Login
                                    </span>
                                </a>

                                <a href="{{ route('register') }}"
                                class="theme-btn flex min-w-[90px] snap-start flex-col items-center justify-center gap-1 rounded-xl px-3 py-3">

                                    <x-heroicon-o-user-plus class="size-6" />

                                    <span class="text-[10px] font-semibold">
                                        Sign Up
                                    </span>
                                </a>

                            @endauth

                        </div>

                        <!-- Slider hint -->
                        @auth
                            <div class="mt-2 text-center text-[9px] text-slate-500">
                                Swipe to see more
                            </div>
                        @endauth

                    </div>

                </details>

            </div>

        </div>
    </nav>

    <livewire:shop-assistant-widget />
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