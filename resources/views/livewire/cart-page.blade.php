<section class="mx-auto w-full max-w-7xl space-y-6 pb-8 md:space-y-7">
    <div class="theme-card rounded-[28px] border border-slate-800/80 p-4 shadow-xl sm:p-5 md:p-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">Cart</p>
                <h1 class="mt-2 text-2xl font-black text-white sm:text-3xl">
                    Shopping bag ({{ $cartItems->sum('qty') }} {{ Str::plural('item', $cartItems->sum('qty')) }})
                </h1>
            </div>

            <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.18em] theme-text transition hover:underline">
                continue shopping
                <x-heroicon-o-arrow-right class="size-4" />
            </a>
        </div>
    </div>

    @if($cartItems->isNotEmpty())
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.5fr)_380px]">
            <div class="space-y-4">
                @foreach($cartItems as $item)
                    <div wire:key="cart-item-{{ $item['cart_key'] }}" class="theme-card rounded-[28px] border border-slate-800/80 p-4 shadow-xl sm:p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-4">
                                <a href="{{ route('product.detail', $item['slug'] ?? $item['id']) }}" class="block flex-shrink-0 overflow-hidden rounded-2xl border border-slate-800 bg-slate-950">
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="h-24 w-24 object-cover sm:h-28 sm:w-28" />
                                </a>

                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('product.detail', $item['slug'] ?? $item['id']) }}" class="block truncate text-base font-black text-white hover:text-(--accent-text) transition-colors">
                                        {{ $item['name'] }}
                                    </a>
                                    @if($item['variant_label'])
                                        <p class="mt-1 text-xs text-slate-400">{{ $item['variant_label'] }}</p>
                                    @endif
                                    <p class="mt-1 text-sm text-slate-400">Unit price: <span class="font-semibold text-slate-200">₹{{ number_format((float) $item['unit'], 0) }}</span></p>
                                    <p class="mt-2 text-lg font-black text-white">₹{{ number_format((float) $item['total'], 0) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-4 sm:justify-end">
                                <div class="flex items-center gap-3 rounded-full border border-slate-800 bg-slate-950/60 px-2 py-1.5">
                                    <button wire:click="updateQuantity({{ $item['id'] }}, -1, {{ $item['variant_id'] ?? 'null' }})" type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900 text-lg font-bold text-slate-200 transition hover:text-white">
                                        −
                                    </button>

                                    <span class="w-8 text-center text-sm font-black text-white">{{ $item['qty'] }}</span>

                                    <button wire:click="updateQuantity({{ $item['id'] }}, 1, {{ $item['variant_id'] ?? 'null' }})" @if($item['qty'] >= $item['max']) disabled @endif type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900 text-lg font-bold text-slate-200 transition hover:text-white disabled:cursor-not-allowed disabled:opacity-40">
                                        +
                                    </button>
                                </div>

                                <button wire:click="removeItem({{ $item['id'] }}, {{ $item['variant_id'] ?? 'null' }})" type="button" class="text-sm font-bold text-rose-400 transition hover:underline">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if(isset($lowestPriceProducts) && $lowestPriceProducts->isNotEmpty())
                    <div class="theme-card rounded-[28px] border border-slate-800/80 p-4 shadow-xl sm:p-5">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h2 class="text-lg font-black text-white">Lowest price deals</h2>
                            <a href="{{ route('shop.index') }}" class="text-[10px] font-black uppercase tracking-[0.18em] theme-text hover:underline">
                                view all
                            </a>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach($lowestPriceProducts as $product)
                                @php
                                    $startingPrice = $product->variants->isNotEmpty()
                                        ? $product->variants->map(fn ($variant) => (float) ($variant->price ?? $product->final_price))->min()
                                        : (float) $product->final_price;
                                @endphp
                                <div wire:key="lowest-{{ $product->id }}" class="theme-inner rounded-2xl border border-slate-800/80 p-3">
                                    <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950">
                                        @if($product->images->first()?->image)
                                            <img src="{{ asset('storage/' . $product->images->first()->image) }}" alt="{{ $product->name }}" class="h-28 w-full object-cover">
                                        @else
                                            <div class="flex h-28 items-center justify-center text-[10px] font-black uppercase tracking-[0.18em] text-slate-500">
                                                Image
                                            </div>
                                        @endif
                                    </div>

                                    <h3 class="mt-3 text-sm font-black text-white line-clamp-2">{{ $product->name }}</h3>
                                    <p class="mt-1 text-sm font-bold theme-text">{{ $product->variants->isNotEmpty() ? 'From ' : '' }}₹{{ number_format($startingPrice, 0) }}</p>

                                    @if($product->variants->isNotEmpty())
                                        <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="theme-btn mt-3 block w-full rounded-xl px-3 py-2 text-center text-[11px] font-black uppercase tracking-wide">
                                            Choose options
                                        </a>
                                    @else
                                        <button wire:click="updateQuantity({{ $product->id }}, 1)" type="button" class="theme-btn mt-3 w-full rounded-xl px-3 py-2 text-[11px] font-black uppercase tracking-wide">
                                            Add to cart
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="space-y-5">
                <div class="theme-card rounded-[28px] border border-slate-800/80 p-5 shadow-xl">
                    <h2 class="text-lg font-black text-white">Order summary</h2>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between text-slate-300">
                            <span>Subtotal</span>
                            <span class="font-semibold text-white">₹{{ number_format((float) $subtotal, 0) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-slate-300">
                            <span>Delivery</span>
                            <span class="font-semibold text-white">₹{{ number_format((float) $shippingFee, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-slate-300">
                            <span>Tax</span>
                            <span class="font-semibold text-white">₹{{ number_format((float) $tax, 0) }}</span>
                        </div>

                        <div class="border-t border-slate-800/80 pt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-base font-black text-white">Total</span>
                                <span class="text-xl font-black text-white">₹{{ number_format((float) $total, 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 text-[11px] leading-5 text-slate-500">Delivery charges and tax rules are not configured yet; no extra fees are included.</p>

                    <a href="{{ route('checkout.index') }}" class="theme-btn mt-5 flex w-full items-center justify-center rounded-2xl px-4 py-3 text-sm font-black uppercase tracking-wide">
                        Proceed to checkout
                    </a>

                    <a href="{{ route('shop.index') }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-sm font-bold text-slate-200 transition hover:text-white">
                        Continue shopping
                        <x-heroicon-o-arrow-right class="size-4" />
                    </a>
                </div>

            </aside>
        </div>
    @else
        <div class="theme-card rounded-[32px] border border-slate-800/80 p-10 text-center shadow-xl">
            <p class="text-5xl">🛒</p>
            <h2 class="mt-5 text-2xl font-black text-white">Your cart is empty</h2>
            <p class="mt-2 text-sm text-slate-400">Add a few favourites and come back here anytime.</p>
            <a href="{{ route('shop.index') }}" class="theme-btn mt-6 inline-flex rounded-full px-5 py-3 text-xs font-black uppercase tracking-[0.2em]">
                Start shopping
            </a>
        </div>
    @endif
</section>