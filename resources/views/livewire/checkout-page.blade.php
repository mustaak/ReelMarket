<main class="mx-auto w-full max-w-6xl space-y-6 pb-10 theme-card p-6">
    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-slate-800/80 pb-5">
        <div class="min-w-0">
            <nav aria-label="Checkout progress" class="mb-3 flex items-center gap-2 text-[11px] font-medium text-slate-500">
                <a href="{{ route('cart.index') }}" class="transition hover:text-slate-200">Cart</a>
                <x-heroicon-o-chevron-right class="size-3" />
                <span aria-current="step" class="text-slate-200">Checkout</span>
            </nav>
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] theme-text">Cash on delivery</p>
            <h1 class="mt-1 font-display text-2xl font-semibold text-white sm:text-3xl">Delivery details</h1>
            <p class="mt-1 text-sm text-slate-400">Where should we bring your order?</p>
        </div>
        <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white">
            <x-heroicon-o-arrow-left class="size-4" />
            Edit cart
        </a>
    </header>

    <form wire:submit="placeOrder" class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_360px] lg:gap-12">
        <div class="min-w-0">
            <section aria-labelledby="delivery-heading">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-full border border-slate-700 text-[11px] font-bold text-slate-300">1</span>
                    <h2 id="delivery-heading" class="text-sm font-bold text-white">Contact and address</h2>
                </div>
                <div class="grid gap-x-4 gap-y-5 sm:grid-cols-2">
                    <div>
                        <label for="customerName" class="mb-2 block text-xs font-semibold text-slate-300">Full name <span class="text-rose-400">*</span></label>
                        <input id="customerName" wire:model="customerName" autocomplete="name" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20" placeholder="Name on delivery">
                        @error('customerName') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="customerPhone" class="mb-2 block text-xs font-semibold text-slate-300">Phone <span class="text-rose-400">*</span></label>
                        <input id="customerPhone" wire:model="customerPhone" type="tel" autocomplete="tel" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20" placeholder="Contact number">
                        @error('customerPhone') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="customerEmail" class="mb-2 block text-xs font-semibold text-slate-300">Email <span class="text-rose-400">*</span></label>
                        <input id="customerEmail" wire:model="customerEmail" type="email" autocomplete="email" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20" placeholder="Order confirmation email">
                        @error('customerEmail') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="addressLine1" class="mb-2 block text-xs font-semibold text-slate-300">Address <span class="text-rose-400">*</span></label>
                        <input id="addressLine1" wire:model="addressLine1" autocomplete="address-line1" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20" placeholder="House number and street">
                        @error('addressLine1') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="addressLine2" class="mb-2 block text-xs font-semibold text-slate-300">Apartment, suite, landmark <span class="font-normal text-slate-500">Optional</span></label>
                        <input id="addressLine2" wire:model="addressLine2" autocomplete="address-line2" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20" placeholder="Additional address details">
                    </div>
                    <div>
                        <label for="city" class="mb-2 block text-xs font-semibold text-slate-300">City <span class="text-rose-400">*</span></label>
                        <input id="city" wire:model="city" autocomplete="address-level2" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20">
                        @error('city') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="state" class="mb-2 block text-xs font-semibold text-slate-300">State / region <span class="text-rose-400">*</span></label>
                        <input id="state" wire:model="state" autocomplete="address-level1" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20">
                        @error('state') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="postalCode" class="mb-2 block text-xs font-semibold text-slate-300">Postal code <span class="text-rose-400">*</span></label>
                        <input id="postalCode" wire:model="postalCode" autocomplete="postal-code" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20">
                        @error('postalCode') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="country" class="mb-2 block text-xs font-semibold text-slate-300">Country <span class="text-rose-400">*</span></label>
                        <input id="country" wire:model="country" autocomplete="country-name" class="theme-inner w-full rounded-lg border border-slate-700/80 px-3.5 py-3 text-sm text-white outline-none transition focus:border-(--accent-primary) focus:ring-2 focus:ring-(--accent-primary)/20">
                        @error('country') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section aria-labelledby="payment-heading" class="mt-8 border-t border-slate-800/80 pt-6">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-full border border-slate-700 text-[11px] font-bold text-slate-300">2</span>
                    <h2 id="payment-heading" class="text-sm font-bold text-white">Payment method</h2>
                </div>
                <div class="flex items-center gap-3 rounded-lg border border-(--accent-primary)/50 bg-white/[0.02] px-4 py-4">
                    <span class="flex size-5 shrink-0 items-center justify-center rounded-full border border-(--accent-primary)">
                        <span class="size-2.5 rounded-full bg-(--accent-primary)"></span>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold text-white">Cash on delivery</span>
                        <span class="mt-0.5 block text-xs text-slate-400">Pay when your order arrives.</span>
                    </span>
                </div>
            </section>
        </div>

        <aside class="theme-card h-fit space-y-5 rounded-xl border border-slate-800/80 p-4 sm:p-5 lg:sticky lg:top-24">
            <section aria-labelledby="summary-heading">
                <div class="flex items-center justify-between gap-3 border-b border-slate-800/80 pb-3">
                    <h2 id="summary-heading" class="text-sm font-bold text-white">Order summary</h2>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ $cartItems->sum('qty') }} items</span>
                </div>
                <ul class="divide-y divide-slate-800/70">
                    @foreach($cartItems as $item)
                        <li wire:key="checkout-item-{{ $item['id'] }}" class="flex items-center justify-between gap-3 py-3">
                            <span class="flex min-w-0 items-center gap-3">
                                <span class="theme-inner flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-800">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="" class="size-full object-cover">
                                    @else
                                        <x-heroicon-o-shopping-bag class="size-5 text-slate-500" />
                                    @endif
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-xs font-semibold text-slate-200">{{ $item['name'] }}</span>
                                    <span class="mt-1 block text-[11px] text-slate-500">Qty {{ $item['qty'] }} × ₹{{ number_format((float) $item['unit'], 2) }}</span>
                                </span>
                            </span>
                            <span class="shrink-0 text-xs font-semibold text-white">₹{{ number_format((float) $item['total'], 2) }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4 rounded-lg border border-slate-800/80 p-3">
                    <label
                        for="couponCode"
                        class="mb-2 block text-xs font-semibold text-slate-300"
                    >
                        Coupon code
                    </label>

                    <div class="flex gap-2">
                        <input
                            id="couponCode"
                            type="text"
                            wire:model="couponCode"
                            placeholder="Enter coupon code"
                            class="theme-inner min-w-0 flex-1 rounded-lg border border-slate-700/80 px-3 py-2.5 text-xs text-white outline-none"
                        >

                        <button
                            type="button"
                            wire:click="applyCoupon"
                            wire:loading.attr="disabled"
                            wire:target="applyCoupon"
                            class="theme-btn shrink-0 rounded-lg px-4 py-2.5 text-xs font-bold disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="applyCoupon">
                                Apply
                            </span>

                            <span wire:loading wire:target="applyCoupon">
                                Applying...
                            </span>
                        </button>
                    </div>

                    @error('couponCode')
                        <p class="mt-2 text-xs text-rose-400">
                            {{ $message }}
                        </p>
                    @enderror

                    @if (session('coupon_success'))
                        <p class="mt-2 text-xs text-emerald-400">
                            {{ session('coupon_success') }}
                        </p>
                    @endif
                </div>

                <dl class="space-y-3 border-t border-slate-800 pt-4 text-sm">
                    @if (!empty($discount))
                        <div class="flex justify-between text-emerald-400">
                            <dt>Discount</dt>
                            <dd class="font-semibold">-₹{{ number_format((float) $discount, 2) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-300">
                        <dt>Subtotal</dt>
                        <dd class="font-semibold text-white">₹{{ number_format((float) $subtotal, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <dt>Delivery</dt>
                        <dd class="font-semibold text-white">₹0.00</dd>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <dt>Tax</dt>
                        <dd class="font-semibold text-white">₹0.00</dd>
                    </div>
                    <div class="flex justify-between border-t border-slate-800 pt-3 text-base font-bold text-white">
                        <dt>Total</dt>
                        <dd>₹{{ number_format((float) ($subtotal - ($discount ?? 0)), 2) }}</dd>
                    </div>
                </dl>

                <p class="mt-4 text-[11px] leading-5 text-slate-500">Delivery and tax rules are not configured yet. No extra fees are included.</p>
                @error('cart') <p class="mt-3 text-xs text-rose-400">{{ $message }}</p> @enderror

                <button type="submit" wire:loading.attr="disabled" wire:target="placeOrder" class="theme-btn mt-5 flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-bold disabled:opacity-50">
                    <span wire:loading.remove wire:target="placeOrder">Place COD order</span>
                    <span wire:loading wire:target="placeOrder">Placing order...</span>
                </button>
            </section>
        </aside>
    </form>
</main>
