<div>
    @if ($variant === 'scroll')
        {{-- ============================================================
            HORIZONTAL SCROLL — Mobile (Insta stories style)
        ============================================================ --}}
        <div class="space-y-2">

            {{-- Header --}}
            <div class="flex items-center justify-between gap-2 px-1">
                <h3 class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    Suggested for you
                </h3>
                <span class="text-[10px] font-medium text-slate-600">Creators</span>
            </div>

            {{-- Horizontal scroller --}}
            <div class="no-scrollbar -mx-1 flex snap-x snap-mandatory gap-3 overflow-x-auto px-1 pb-1">

                @forelse ($suggestions as $suggestion)
                    @php
                        $followStatus = $followStatuses[$suggestion->id] ?? null;
                        $reverseFollowAccepted = $reverseFollowStatuses[$suggestion->id] ?? false;

                        $buttonLabel = match (true) {
                            $followStatus === 'accepted' => 'Following',
                            $followStatus === 'pending' => 'Requested',
                            $reverseFollowAccepted => 'Follow back',
                            default => 'Follow',
                        };

                        $isFollowing = in_array($followStatus, ['accepted', 'pending'], true);
                    @endphp

                    <div wire:key="scroll-suggest-{{ $suggestion->id }}"
                        class="theme-inner flex w-[42%] shrink-0 snap-start flex-col items-center gap-2 rounded-2xl border border-slate-800/80 p-3 text-center">

                        {{-- Avatar --}}
                        @if ($suggestion->activeStories->isNotEmpty())
                            <button type="button"
                                wire:click="$dispatch('open-story', { storyId: {{ $suggestion->activeStories->first()->id }} })"
                                aria-label="View {{ $suggestion->name }}'s story"
                                class="relative flex size-14 items-center justify-center rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 p-[2px]">
                                <span class="flex size-full items-center justify-center overflow-hidden rounded-full border-2 border-[var(--bg-inner)] bg-slate-900 text-sm font-black text-white">
                                    @if ($suggestion->profile?->profile_picture)
                                        <img src="{{ asset('storage/' . $suggestion->profile->profile_picture) }}"
                                            alt="{{ $suggestion->name }}" class="size-full object-cover">
                                    @else
                                        {{ strtoupper(substr($suggestion->name, 0, 1)) }}
                                    @endif
                                </span>
                            </button>
                        @else
                            <a href="{{ route('users.show', $suggestion) }}"
                                class="flex size-14 items-center justify-center overflow-hidden rounded-full bg-slate-800 text-sm font-black text-slate-300">
                                @if ($suggestion->profile?->profile_picture)
                                    <img src="{{ asset('storage/' . $suggestion->profile->profile_picture) }}"
                                        alt="{{ $suggestion->name }}" class="size-full object-cover">
                                @else
                                    {{ strtoupper(substr($suggestion->name, 0, 1)) }}
                                @endif
                            </a>
                        @endif

                        {{-- Name + followers --}}
                        <div class="w-full min-w-0">
                            <a href="{{ route('users.show', $suggestion) }}"
                                class="block truncate text-[12px] font-semibold leading-tight text-white hover:text-(--accent-text)">
                                {{ $suggestion->profile?->username ?? $suggestion->name }}
                            </a>
                            <p class="mt-0.5 truncate text-[10px] leading-tight text-slate-500">
                                {{ number_format($suggestion->accepted_followers_count ?? 0) }}
                                {{ \Illuminate\Support\Str::plural('follower', $suggestion->accepted_followers_count ?? 0) }}
                            </p>
                        </div>

                        {{-- Follow button --}}
                        @auth
                            <button type="button"
                                wire:click="toggleFollow({{ $suggestion->id }})"
                                wire:loading.attr="disabled"
                                wire:target="toggleFollow({{ $suggestion->id }})"
                                aria-label="{{ $buttonLabel }} {{ $suggestion->name }}"
                                class="w-full rounded-lg py-1.5 text-[11px] font-bold transition disabled:opacity-50
                                    {{ $isFollowing
                                        ? 'border border-slate-700 text-slate-300'
                                        : 'theme-btn text-black' }}">
                                {{ $buttonLabel }}
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="theme-btn w-full rounded-lg py-1.5 text-[11px] font-bold text-black">
                                Follow
                            </a>
                        @endauth
                    </div>
                @empty
                    <p class="w-full py-4 text-center text-[11px] text-slate-500">
                        You're all caught up.
                    </p>
                @endforelse

            </div>
        </div>
    @else
        {{-- ============================================================
            VERTICAL LIST — Desktop sidebar (Insta style)
        ============================================================ --}}
        <div class="space-y-2">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-2 px-1">
                <h3 class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    Suggested for you
                </h3>
                <span class="text-[10px] font-medium text-slate-600">Creators</span>
            </div>

            {{-- List --}}
            <ul class="space-y-0.5">
                @forelse($suggestions as $suggestion)
                    @php
                        $followStatus = $followStatuses[$suggestion->id] ?? null;
                        $reverseFollowAccepted = $reverseFollowStatuses[$suggestion->id] ?? false;

                        $buttonLabel = match (true) {
                            $followStatus === 'accepted' => 'Following',
                            $followStatus === 'pending' => 'Requested',
                            $reverseFollowAccepted => 'Follow back',
                            default => 'Follow',
                        };

                        $isFollowing = in_array($followStatus, ['accepted', 'pending'], true);
                    @endphp

                    <li wire:key="list-suggest-{{ $suggestion->id }}"
                        class="flex items-center gap-3 rounded-xl px-2 py-2 transition hover:bg-white/[0.03]">

                        {{-- Avatar --}}
                        @if ($suggestion->activeStories->isNotEmpty())
                            <button type="button"
                                wire:click="$dispatch('open-story', { storyId: {{ $suggestion->activeStories->first()->id }} })"
                                aria-label="View {{ $suggestion->name }}'s story"
                                class="relative flex size-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 p-[2px]">
                                <span class="flex size-full items-center justify-center overflow-hidden rounded-full border-2 border-[var(--bg-card)] bg-slate-900 text-xs font-black text-white">
                                    @if ($suggestion->profile?->profile_picture)
                                        <img src="{{ asset('storage/' . $suggestion->profile->profile_picture) }}"
                                            alt="{{ $suggestion->name }}" class="size-full object-cover">
                                    @else
                                        {{ strtoupper(substr($suggestion->name, 0, 1)) }}
                                    @endif
                                </span>
                            </button>
                        @else
                            <a href="{{ route('users.show', $suggestion) }}"
                                class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-800 text-xs font-black text-slate-300">
                                @if ($suggestion->profile?->profile_picture)
                                    <img src="{{ asset('storage/' . $suggestion->profile->profile_picture) }}"
                                        alt="{{ $suggestion->name }}" class="size-full object-cover">
                                @else
                                    {{ strtoupper(substr($suggestion->name, 0, 1)) }}
                                @endif
                            </a>
                        @endif

                        {{-- Name + followers --}}
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('users.show', $suggestion) }}"
                                class="block truncate text-[13px] font-semibold leading-tight text-white hover:text-(--accent-text)">
                                {{ $suggestion->profile?->username ?? $suggestion->name }}
                            </a>
                            <p class="mt-0.5 truncate text-[11px] leading-tight text-slate-500">
                                {{ number_format($suggestion->accepted_followers_count ?? 0) }}
                                {{ \Illuminate\Support\Str::plural('follower', $suggestion->accepted_followers_count ?? 0) }}
                            </p>
                        </div>

                        {{-- Follow button --}}
                        @auth
                            <button type="button"
                                wire:click="toggleFollow({{ $suggestion->id }})"
                                wire:loading.attr="disabled"
                                wire:target="toggleFollow({{ $suggestion->id }})"
                                aria-label="{{ $buttonLabel }} {{ $suggestion->name }}"
                                class="shrink-0 text-[12px] font-bold transition disabled:opacity-50
                                    {{ $isFollowing
                                        ? 'text-slate-400 hover:text-white'
                                        : 'text-(--accent-text) hover:text-(--accent-primary)' }}">
                                {{ $buttonLabel }}
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="shrink-0 text-[12px] font-bold text-(--accent-text) hover:text-(--accent-primary)">
                                Follow
                            </a>
                        @endauth
                    </li>
                @empty
                    <li class="py-6 text-center text-[11px] text-slate-500">
                        You're all caught up.
                    </li>
                @endforelse
            </ul>
        </div>
    @endif
</div>