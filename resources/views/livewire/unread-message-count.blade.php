<span wire:poll.10s class="pointer-events-none absolute -right-2 -top-2 z-10">
    @if($unreadMessageCount > 0)
        <span role="status" aria-label="{{ $unreadMessageCount }} unread messages" class="flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-black leading-none text-white shadow">
            {{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}
        </span>
    @endif
</span>
