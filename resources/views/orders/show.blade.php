<x-layouts.app>
    <main class="mx-auto w-full max-w-4xl space-y-7 pb-10">
        <header class="flex flex-wrap items-end justify-between gap-4 border-b border-slate-800/80 pb-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] theme-text">Order confirmation</p>
                <h1 class="mt-1 font-display text-2xl font-semibold text-white">{{ $order->order_number }}</h1>
                <p class="mt-1 text-xs text-slate-400">Placed {{ $order->created_at->format('M j, Y · g:i A') }}</p>
            </div>
            <span class="rounded-full border border-amber-700/50 bg-amber-950/30 px-3 py-1.5 text-xs font-bold uppercase text-amber-200">{{ str_replace('_', ' ', $order->status) }}</span>
        </header>

        <section class="grid gap-8 border-b border-slate-800/80 pb-7 sm:grid-cols-2">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Delivering to</h2>
                <address class="mt-3 text-sm not-italic leading-6 text-slate-200">
                    <span class="block font-semibold text-white">{{ $order->customer_name }}</span>
                    {{ $order->address_line1 }}<br>
                    @if($order->address_line2) {{ $order->address_line2 }}<br> @endif
                    {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                    {{ $order->country }}<br>
                    {{ $order->customer_phone }}
                </address>
            </div>
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Payment</h2>
                <p class="mt-3 text-sm font-semibold text-white">Cash on delivery</p>
                <p class="mt-1 text-xs text-slate-400">Payment status: {{ str_replace('_', ' ', $order->payment_status) }}</p>
            </div>
        </section>

        <section aria-labelledby="order-items-heading">
            <h2 id="order-items-heading" class="text-sm font-bold text-white">Items</h2>
            <ul class="mt-3 divide-y divide-slate-800/70 border-y border-slate-800/70">
                @foreach($order->items as $item)
                    <li class="flex items-center justify-between gap-4 py-4">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-white">{{ $item->product_name }}</span>
                            <span class="mt-1 block text-xs text-slate-400">Qty {{ $item->quantity }} × ₹{{ number_format((float) $item->unit_price, 2) }}</span>
                        </span>
                        <span class="shrink-0 text-sm font-semibold text-white">₹{{ number_format((float) $item->line_total, 2) }}</span>
                    </li>
                @endforeach
            </ul>
            <dl class="ml-auto mt-4 max-w-xs space-y-2 text-sm">
                <div class="flex justify-between text-slate-400"><dt>Subtotal</dt><dd>₹{{ number_format((float) $order->subtotal, 2) }}</dd></div>
                <div class="flex justify-between text-slate-400"><dt>Delivery</dt><dd>₹{{ number_format((float) $order->shipping_fee, 2) }}</dd></div>
                <div class="flex justify-between text-slate-400"><dt>Tax</dt><dd>₹{{ number_format((float) $order->tax_amount, 2) }}</dd></div>
                <div class="flex justify-between border-t border-slate-800 pt-3 font-bold text-white"><dt>Total due on delivery</dt><dd>₹{{ number_format((float) $order->total, 2) }}</dd></div>
            </dl>
        </section>

        <footer class="flex flex-wrap gap-3 border-t border-slate-800/80 pt-5">
            <a href="{{ route('orders.index') }}" class="rounded-lg border border-slate-700 px-4 py-2.5 text-xs font-semibold text-slate-200 hover:bg-white/5">My orders</a>
            <a href="{{ route('shop.index') }}" class="theme-btn rounded-lg px-4 py-2.5 text-xs font-bold">Continue shopping</a>
        </footer>
    </main>
</x-layouts.app>