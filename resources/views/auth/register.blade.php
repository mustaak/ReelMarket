<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | YourBrand</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0b0813] text-slate-100">
    <div class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="w-full max-w-[420px]">
            <div class="rounded-2xl border border-slate-800 bg-[#140e26] p-7 shadow-[0_10px_30px_rgba(0,0,0,0.35)]">
                <div class="mb-7 text-center">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-[#f9a8d4] via-[#c084fc] to-[#60a5fa] text-xl font-black text-white shadow-sm">Y</div>
                    <h2 class="text-[2rem] font-black tracking-[-0.06em] text-white">YourBrand</h2>
                    <p class="mt-2 text-sm text-slate-400">Create an account to explore the latest styles.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                               class="w-full rounded-lg border border-slate-700 bg-[#1a1333] px-3.5 py-3 text-sm text-white placeholder:text-slate-400 focus:border-slate-500 focus:outline-none"
                               placeholder="Full name">
                        @error('name')
                            <p class="mt-2 text-xs font-medium text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               class="w-full rounded-lg border border-slate-700 bg-[#1a1333] px-3.5 py-3 text-sm text-white placeholder:text-slate-400 focus:border-slate-500 focus:outline-none"
                               placeholder="Email address">
                        @error('email')
                            <p class="mt-2 text-xs font-medium text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <input id="password" name="password" type="password" required
                               class="w-full rounded-lg border border-slate-700 bg-[#1a1333] px-3.5 py-3 text-sm text-white placeholder:text-slate-400 focus:border-slate-500 focus:outline-none"
                               placeholder="Password">
                        @error('password')
                            <p class="mt-2 text-xs font-medium text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full rounded-lg border border-slate-700 bg-[#1a1333] px-3.5 py-3 text-sm text-white placeholder:text-slate-400 focus:border-slate-500 focus:outline-none"
                               placeholder="Confirm password">
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-[#f59e0b] px-4 py-2.5 text-sm font-semibold text-black shadow-sm transition hover:brightness-110">
                        Sign Up
                    </button>

                    <div class="flex items-center gap-3 py-1">
                        <div class="h-px flex-1 bg-slate-700"></div>
                        <span class="text-[10px] font-semibold uppercase tracking-[0.25em] text-slate-400">or</span>
                        <div class="h-px flex-1 bg-slate-700"></div>
                    </div>

                    <button type="button" class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-700 bg-[#1a1333] px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-[#22193d]">
                        <span class="text-base font-bold text-[#1877f2]">f</span>
                        Sign up with Facebook
                    </button>
                </form>
            </div>

            <div class="mt-4 rounded-2xl border border-slate-800 bg-[#140e26] p-4 text-center text-sm text-slate-300 shadow-[0_10px_30px_rgba(0,0,0,0.2)]">
                Have an account?
                <a href="{{ route('login') }}" class="ml-1 font-semibold text-amber-400">Log in</a>
            </div>
        </div>
    </div>
</body>
</html>
