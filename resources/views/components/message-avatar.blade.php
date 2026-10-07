@props([
    'user' => null,
    'avatar' => null,
    'online' => false,
    'size' => '11',
])

<span class="theme-soft-bg theme-text relative flex size-{{ $size }}
             shrink-0 items-center justify-center overflow-hidden rounded-full
             text-sm font-bold">

    @if($avatar)
        <img
            src="{{ asset('storage/' . $avatar) }}"
            alt=""
            class="size-full object-cover"
        >
    @else
        {{ strtoupper(substr($user?->name ?? 'C', 0, 1)) }}
    @endif

    <span @class([
        'absolute bottom-0 right-0 size-3 rounded-full border-2 border-(--bg-inner)',
        'bg-emerald-400' => $online,
        'bg-slate-500' => !$online,
    ])></span>
</span>