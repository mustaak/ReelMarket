<x-layouts.app>
    <main class="mx-auto w-full max-w-6xl space-y-6 pb-10 theme-card p-6">
        <header class="flex items-end justify-between gap-4 border-b border-slate-800/80 pb-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] theme-text">Account</p>
                <h1 class="mt-1 font-display text-2xl font-semibold text-white">Edit profile</h1>
            </div>
            <a href="{{ route('profile') }}" class="text-xs font-semibold text-slate-400 transition hover:text-white">Cancel</a>
        </header>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-7">
            @csrf
            @method('PATCH')

            <div class="flex items-center gap-4 border-b border-slate-800/70 pb-6">
                <span class="theme-soft-bg theme-text flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-full text-xl font-bold">
                    @if($profile->profile_picture)
                        <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="{{ $user->name }}" class="size-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">{{ $user->name }}</p>
                    <label for="profile_picture" class="mt-1 inline-flex cursor-pointer text-xs font-bold theme-text hover:underline">Change profile photo</label>
                    <input id="profile_picture" name="profile_picture" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">
                    @error('profile_picture') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="name" class="mb-2 block text-xs font-semibold text-slate-300">Name <span class="text-rose-400">*</span></label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20">
                @error('name') <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="bio" class="mb-2 block text-xs font-semibold text-slate-300">Bio</label>
                <textarea id="bio" name="bio" rows="4" maxlength="255" class="theme-inner w-full resize-y rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-slate-500 focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20" placeholder="A little about you">{{ old('bio', $profile->bio) }}</textarea>
                @error('bio') <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-800/80 pt-5">
                <a href="{{ route('profile') }}" class="rounded-lg border border-slate-700 px-4 py-2.5 text-xs font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white">Cancel</a>
                <button type="submit" class="theme-btn rounded-lg px-4 py-2.5 text-xs font-bold">Save changes</button>
            </div>
        </form>
    </main>
</x-layouts.app>