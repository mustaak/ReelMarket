<main wire:poll.15s class="mx-auto w-full max-w-3xl space-y-6 pb-10">
    <header class="flex items-end justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] theme-text">YourBrand</p>
            <h1 class="mt-1 font-display text-2xl font-semibold text-white">Activity</h1>
        </div>
        @if($unreadCount > 0)
            <button type="button" wire:click="markAllAsRead" class="text-xs font-semibold text-slate-400 transition hover:text-white">Mark all as read</button>
        @endif
    </header>

    <ul class="divide-y divide-slate-800/70">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $actorPhoto = $data['actor_photo'] ?? null;
                $actorName = $data['actor_name'] ?? 'Someone';
            @endphp
            <li wire:key="notification-{{ $notification->id }}">
                <button type="button" wire:click="openNotification('{{ $notification->id }}')" @class([
                    'flex w-full items-center gap-3 px-3 py-4 text-left transition hover:bg-white/[0.03] sm:px-4',
                    'bg-white/[0.025]' => ! $notification->read_at,
                ])>
                    <span class="theme-soft-bg theme-text relative flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-full text-sm font-bold">
                        @if($actorPhoto)
                            <img src="{{ asset('storage/' . $actorPhoto) }}" alt="" class="size-full object-cover">
                        @else
                            {{ strtoupper(substr($actorName, 0, 1)) }}
                        @endif
                        @if(!$notification->read_at)
                            <span class="absolute right-0 top-0 size-2.5 rounded-full bg-rose-500 ring-2 ring-[#140e26]"></span>
                        @endif
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm leading-5 text-slate-200"><span class="font-bold text-white">{{ $actorName }}</span> {{ $data['message'] ?? 'interacted with you.' }}</span>
                        <time datetime="{{ $notification->created_at->toIso8601String() }}" class="mt-1 block text-[11px] text-slate-500">{{ $notification->created_at->diffForHumans() }}</time>
                    </span>
                    @if(!$notification->read_at)
                        <span class="size-2 shrink-0 rounded-full bg-rose-500" aria-hidden="true"></span>
                    @endif
                </button>
            </li>
        @empty
            <li class="flex flex-col items-center py-16 text-center">
                <x-heroicon-o-bell class="size-9 text-slate-600" />
                <p class="mt-3 text-sm font-semibold text-white">You’re all caught up</p>
                <p class="mt-1 text-xs text-slate-400">New follows, likes, and comments will appear here.</p>
            </li>
        @endforelse
    </ul>

    @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
    @endif
</main>
