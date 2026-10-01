<div>
    @php
        $money = fn ($v) => '₹' . number_format($v, fmod((float) $v, 1) === 0.0 ? 0 : 2);
    @endphp

    @if ($open)
        <div class="fixed inset-0 z-[60]"
             role="dialog" aria-modal="true" aria-label="Shopping cart"
             wire:keydown.escape.window="close">

            <div class="absolute inset-0 bg-black/60" wire:click="close"></div>

            <aside class="theme-card absolute inset-y-0 right-0 flex w-full max-w-md flex-col border-l border-slate-800/80 text-slate-100 shadow-2xl">
                <header class="flex items-center justify-between border-b border-slate-800/80 px-4 py-3">
                    <h2 class="text-lg font-black tracking-wide">
                        Your cart
                        @if ($count > 0)
                            <span class="text-slate-400">({{ $count }})</span>
                        @endif
                    </h2>
                    <button type="button" wire:click="close" aria-label="Close cart"
                            class="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white">
                        <x-heroicon-o-x-mark class="size-6" />
                    </button>
                </header>

                @if ($notice)
                    <p class="theme-inner theme-text border-b border-slate-800/80 px-4 py-2 text-sm" role="status">{{ $notice }}</p>
                @endif

                @if ($items->isEmpty())
                    <div class="flex flex-1 flex-col items-center justify-center gap-3 p-8 text-center">
                        <x-heroicon-o-shopping-bag class="size-12 text-slate-500" />
                        <p class="font-bold">Your cart is empty</p>
                        <a href="{{ route('shop.index') }}" wire:click="close"
                           class="theme-btn rounded-xl px-5 py-2.5 text-sm font-bold">
                            Continue shopping
                        </a>
                    </div>
                @else
                    <ul class="flex-1 divide-y divide-slate-800/80 overflow-y-auto px-4">
                        @foreach ($items as $line)
                            <li wire:key="line-{{ $line['cart_key'] }}" class="flex gap-3 py-4">
                                <a href="{{ route('product.detail', $line['slug'] ?? $line['id']) }}"
                                   class="theme-inner size-20 shrink-0 overflow-hidden rounded-lg">
                                    @if ($line['image'])
                                        <img src="{{ asset('storage/' . $line['image']) }}" alt=""
                                             class="size-full object-cover">
                                    @else
                                        <span class="theme-text grid size-full place-items-center text-2xl font-black">
                                            {{ mb_substr($line['name'], 0, 1) }}
                                        </span>
                                    @endif
                                </a>

                                <div class="flex min-w-0 flex-1 flex-col">
                                    <a href="{{ route('product.detail', $line['slug'] ?? $line['id']) }}" class="truncate text-sm font-bold text-white">
                                        {{ $line['name'] }}
                                    </a>
                                    @if($line['variant_label'])
                                        <span class="mt-1 text-xs text-slate-400">{{ $line['variant_label'] }}</span>
                                    @endif
                                    <span class="text-xs text-slate-400">{{ $money($line['unit']) }}</span>

                                    <div class="mt-auto flex items-center justify-between pt-2">
                                        <div class="theme-inner flex items-center rounded-lg border border-slate-700">
                                            <button type="button" wire:click="decrement({{ $line['id'] }}, {{ $line['variant_id'] ?? 'null' }})"
                                                    aria-label="Decrease quantity of {{ $line['name'] }}"
                                                    class="px-2.5 py-1 text-slate-300 hover:text-white">−</button>
                                            <span class="w-7 text-center text-sm font-bold">{{ $line['qty'] }}</span>
                                            <button type="button" wire:click="increment({{ $line['id'] }}, {{ $line['variant_id'] ?? 'null' }})"
                                                    @disabled($line['qty'] >= $line['max'])
                                                    aria-label="Increase quantity of {{ $line['name'] }}"
                                                    class="px-2.5 py-1 text-slate-300 hover:text-white disabled:opacity-40">+</button>
                                        </div>
                                        <b class="theme-text text-sm">{{ $money($line['total']) }}</b>
                                    </div>
                                </div>

                                <button type="button" wire:click="remove({{ $line['id'] }}, {{ $line['variant_id'] ?? 'null' }})"
                                        aria-label="Remove {{ $line['name'] }} from cart"
                                        class="self-start rounded-lg p-1.5 text-slate-500 hover:bg-white/5 hover:text-white">
                                    <x-heroicon-o-trash class="size-5" />
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <footer class="border-t border-slate-800/80 p-4">
                        <div class="flex items-baseline justify-between">
                            <span class="text-slate-400">Subtotal</span>
                            <b class="theme-text text-lg">{{ $money($subtotal) }}</b>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Shipping and taxes are calculated at checkout.</p>
                        <button type="button"
                                onclick="window.location.href='{{ route('checkout.index') }}'"
                                class="theme-btn mt-3 w-full rounded-xl px-6 py-3 text-sm font-bold">
                            Checkout
                        </button>
                    </footer>
                @endif
            </aside>
        </div>
    @endif
</div>