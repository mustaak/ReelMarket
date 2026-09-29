<div class="w-full">
    @if ($max > 0)
        <div x-data="{ qty: 1, max: {{ $max }} }" class="flex gap-3">
            <div class="theme-inner flex shrink-0 items-center rounded-xl border border-slate-700">
                <button type="button"
                        @click="qty = Math.max(1, qty - 1)"
                        :disabled="qty <= 1"
                        aria-label="Decrease quantity"
                        class="px-3 py-3 text-slate-300 hover:text-white disabled:opacity-40">−</button>
                <span x-text="qty" class="w-8 text-center font-bold" aria-live="polite"></span>
                <button type="button"
                        @click="qty = Math.min(max, qty + 1)"
                        :disabled="qty >= max"
                        aria-label="Increase quantity"
                        class="px-3 py-3 text-slate-300 hover:text-white disabled:opacity-40">+</button>
            </div>

            <button type="button"
                    @click="$wire.add(qty)"
                    wire:loading.attr="disabled"
                    class="theme-btn flex-1 whitespace-nowrap rounded-xl px-6 py-3 font-bold disabled:opacity-60">
                <span wire:loading.remove wire:target="add">Add to cart</span>
                <span wire:loading wire:target="add">Adding…</span>
            </button>
        </div>
    @else
        <button type="button" disabled
                class="theme-inner w-full cursor-not-allowed rounded-xl px-6 py-3 font-bold text-slate-500">
            Out of stock
        </button>
    @endif
</div>