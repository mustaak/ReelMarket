<!DOCTYPE html>
<html lang="en" data-theme="{{ session('user_bg', 'midnight') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in | ReelMarket</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="editorial-auth min-h-screen bg-page text-ink">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-[440px]">
            <div class="theme-card p-7 sm:p-9">
                <div class="mb-8 text-center">
                    <div class="editorial-brand-mark mx-auto mb-4 inline-flex size-12 items-center justify-center rounded-2xl text-xl font-black text-ink">R</div>
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.24em] theme-text">Shop · Share · Discover</p>
                    <h1 class="font-display text-4xl font-bold tracking-tight text-ink">ReelMarket</h1>
                    <p class="mt-2 text-sm text-mute">Your next find is closer than you think.</p>
                </div>

                @if (session('status'))
                    <p class="mb-4 rounded-xl bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700" role="status">
                        {{ session('status') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required
                               class="theme-inner w-full rounded-xl border border-line px-4 py-3.5 text-sm text-ink outline-none transition focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20"
                               placeholder="Email address">
                        @error('email')
                            <p class="mt-2 text-xs font-medium text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="theme-inner w-full rounded-xl border border-line px-4 py-3.5 text-sm text-ink outline-none transition focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20"
                               placeholder="Password">
                        @error('password')
                            <p class="mt-2 text-xs font-medium text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-3 text-sm">
                        <label for="remember" class="flex items-center gap-2 text-mute">
                            <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))
                                   class="size-4 rounded border-line accent-(--accent-primary)">
                            Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="font-semibold theme-text hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit" class="theme-btn w-full rounded-xl px-4 py-3.5 text-sm font-bold shadow-sm transition hover:brightness-105">
                        Log In
                    </button>
                </form>
            </div>

            <div class="theme-card mt-4 p-4 text-center text-sm text-mute">
                Don't have an account?
                <a href="{{ route('register') }}" class="ml-1 font-semibold theme-text hover:underline">Sign up</a>
            </div>
        </div>
    </div>
</body>
</html>
