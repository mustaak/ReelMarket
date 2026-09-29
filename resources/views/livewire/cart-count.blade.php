<span>
    @if ($count > 0)
        <span class="theme-btn absolute -right-2 -top-2 grid min-w-5 place-items-center rounded-full px-1 text-[11px] font-black leading-5">
            {{ $count > 99 ? '99+' : $count }}
        </span>
        <span class="sr-only">{{ $count }} {{ \Illuminate\Support\Str::plural('item', $count) }} in cart</span>
    @endif
</span>