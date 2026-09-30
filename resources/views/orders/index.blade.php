<x-layouts.app>
    <main class="mx-auto w-full max-w-4xl space-y-6 pb-10">
        <header class="flex items-end justify-between gap-4 border-b border-slate-800/80 pb-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] theme-text">YourBrand</p>
                <h1 class="mt-1 font-display text-2xl font-semibold text-white">My orders</h1>
            </div>
            <a href="{{ route('shop.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white">Continue shopping</a>
        </header>

        @forelse($orders as $order)
            <a href="{{ route('orders.show', $order) }}" class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800/70 py-5 transition hover:bg-white/[0.02]">
                <span class="min-w-0">
                    <span class="block truncate text-sm font-bold text-white">{{ $order->order_number }}</span>
                    <span class="mt-1 block text-xs text-slate-400">{{ $order->created_at->format('M j, Y') }} · {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}</span>
                </span>
                <span class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-white">₹{{ number_format((float) $order->total, 2) }}</span>
                    <span class="rounded-full border border-slate-700 px-2.5 py-1 text-[10px] font-bold uppercase text-slate-300">{{ str_replace('_', ' ', $order->status) }}</span>
                    <x-heroicon-o-chevron-right class="size-4 text-slate-500" />
                </span>
            </a>
        @empty
            <section class="py-16 text-center">
                <x-heroicon-o-receipt-refund class="mx-auto size-10 text-slate-600" />
                <h2 class="mt-4 text-sm font-semibold text-white">No orders yet</h2>
                <p class="mt-1 text-xs text-slate-400">Orders you place will appear here.</p>
                <a href="{{ route('shop.index') }}" class="theme-btn mt-5 inline-flex rounded-lg px-4 py-2.5 text-xs font-bold">Explore shop</a>
            </section>
        @endforelse

        @if($orders->hasPages())
            <div class="pt-4">{{ $orders->links() }}</div>
        @endif
    </main>
</x-layouts.app>