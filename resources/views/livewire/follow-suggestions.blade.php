<div class="space-y-3">
    <div class="flex items-center justify-between gap-2">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Suggested for you</h3>
        <span class="text-[10px] text-slate-500">Creators</span>
    </div>

    <ul class="divide-y divide-slate-800/70">
        @forelse($suggestions as $suggestion)
            @php $followStatus = $followStatuses[$suggestion->id] ?? null; @endphp
            <li wire:key="suggested-user-{{ $suggestion->id }}" class="flex items-center gap-2.5 py-3 first:pt-1">
                <a href="{{ route('users.show', $suggestion) }}" class="theme-soft-bg theme-text flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full text-sm font-black">
                    @if($suggestion->profile?->profile_picture)
                        <img src="{{ asset('storage/' . $suggestion->profile->profile_picture) }}" alt="" class="size-full object-cover">
                    @else
                        {{ strtoupper(substr($suggestion->name, 0, 1)) }}
                    @endif
                </a>

                <div class="min-w-0 flex-1">
                    <a href="{{ route('users.show', $suggestion) }}" class="block truncate text-xs font-bold text-white hover:text-(--accent-text)">{{ $suggestion->profile?->username ?? $suggestion->name }}</a>
                    <p class="mt-0.5 truncate text-[10px] text-slate-500">
                        {{ number_format($suggestion->accepted_followers_count) }} {{ \Illuminate\Support\Str::plural('follower', $suggestion->accepted_followers_count) }}
                    </p>
                </div>

                @auth
                    @php
                        $reverseFollowAccepted = $reverseFollowStatuses[$suggestion->id] ?? false;

                        $buttonLabel = match (true) {
                            $followStatus === 'accepted' => 'Following',
                            $followStatus === 'pending' => 'Requested',
                            $reverseFollowAccepted => 'Follow Back',
                            default => 'Follow',
                        };
                    @endphp

                    <button
                        type="button"
                        wire:click="toggleFollow({{ $suggestion->id }})"
                        wire:loading.attr="disabled"
                        wire:target="toggleFollow({{ $suggestion->id }})"
                        aria-label="{{ $buttonLabel }} {{ $suggestion->name }}"
                        class="shrink-0 rounded-lg px-2.5 py-1.5 text-[10px] font-bold transition disabled:opacity-50
                            {{ in_array($followStatus, ['accepted', 'pending']) ? 'border border-slate-700 text-slate-300' : 'theme-btn' }}"
                    >
                        {{ $buttonLabel }}
                    </button>
                @else
                    <a href="{{ route('login') }}" class="theme-btn shrink-0 rounded-lg px-2.5 py-1.5 text-[10px] font-bold">Follow</a>
                @endauth
            </li>
        @empty
            <li class="py-4 text-center text-xs text-slate-500">You’re all caught up.</li>
        @endforelse
    </ul>
</div>
