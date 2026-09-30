<span wire:poll.15s class="pointer-events-none absolute -right-2 -top-2 z-10">
    @if($unreadCount > 0)
        <span role="status" aria-label="{{ $unreadCount }} unread notifications" class="flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-black leading-none text-white shadow">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
    @endif
</span>
