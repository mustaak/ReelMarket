<div class="mx-auto w-full max-w-7xl space-y-5 md:space-y-6">
    <section class="theme-card overflow-hidden rounded-[28px] border border-slate-800/80 shadow-2xl">
        <div class="flex flex-col gap-4 p-4 sm:p-5 md:p-6">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">Shop</p>
                    <h1 class="mt-2 text-2xl font-black tracking-tight text-white sm:text-3xl">Curated essentials for every day</h1>
                </div>

                <button type="button" onclick="Livewire.dispatch('open-cart')" class="theme-btn inline-flex items-center justify-center gap-2 self-start rounded-full px-4 py-2.5 text-xs font-black uppercase tracking-wide md:self-auto">
                    <x-heroicon-o-shopping-cart class="size-4" />
                    Cart
                    <span class="rounded-full bg-black/10 px-1.5 py-0.5 text-[10px] font-black text-black"><livewire:cart-count /></span>
                </button>
            </div>

            <div class="relative">
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search products, brands, SKU..."
                    class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 py-3 pl-11 pr-4 text-sm text-white placeholder:text-slate-500 focus:border-(--accent-primary) focus:outline-none" />
                <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-slate-500" />
            </div>

            <div class="no-scrollbar flex gap-2 overflow-x-auto pb-1">
                <button wire:click="$set('selectedCategory', 'All')"
                    class="shrink-0 rounded-full px-3.5 py-2 text-[11px] font-black uppercase tracking-wide transition {{ $selectedCategory === 'All' ? 'theme-btn text-black shadow-lg' : 'border border-slate-800 bg-slate-950/60 text-slate-300 hover:text-white' }}">
                    All products
                </button>

                @foreach($categories as $category)
                    @php $categoryKey = $category->slug ?? $category->id; @endphp
                    <button wire:click="$set('selectedCategory', '{{ $categoryKey }}')"
                        class="shrink-0 rounded-full px-3.5 py-2 text-[11px] font-black uppercase tracking-wide transition {{ $selectedCategory == $categoryKey ? 'theme-btn text-black shadow-lg' : 'border border-slate-800 bg-slate-950/60 text-slate-300 hover:text-white' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="theme-card rounded-[28px] border border-slate-800/80 p-4 shadow-xl xl:sticky xl:top-24 xl:h-fit">
            <div class="mb-4 flex items-center justify-between border-b border-slate-800/80 pb-3">
                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">Filters</span>
                <button wire:click="clearFilters" class="text-[11px] font-bold text-rose-400 transition hover:underline">
                    Reset
                </button>
            </div>

            <div class="space-y-5">
                @if(count($brands) > 0)
                    <div class="space-y-2">
                        <span class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Brands</span>
                        <div class="max-h-44 space-y-2 overflow-y-auto pr-1 no-scrollbar">
                            @foreach($brands as $brand)
                                <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-300 hover:text-white">
                                    <input type="checkbox" wire:model.live="selectedBrands" value="{{ $brand->id }}" class="h-4 w-4 rounded border-slate-700 bg-slate-950 text-(--accent-primary) focus:ring-0">
                                    <span>{{ $brand->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="space-y-2 border-t border-slate-800/80 pt-4">
                    <span class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Price range</span>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" wire:model.live.debounce.400ms="minPrice" placeholder="Min" class="w-full rounded-xl border border-slate-800 bg-slate-950/70 px-3 py-2 text-xs text-white placeholder:text-slate-500 focus:outline-none">
                        <input type="number" wire:model.live.debounce.400ms="maxPrice" placeholder="Max" class="w-full rounded-xl border border-slate-800 bg-slate-950/70 px-3 py-2 text-xs text-white placeholder:text-slate-500 focus:outline-none">
                    </div>
                </div>

                <div class="border-t border-slate-800/80 pt-4">
                    <label class="flex cursor-pointer items-center gap-2 text-sm font-semibold text-slate-300">
                        <input type="checkbox" wire:model.live="inStockOnly" class="h-4 w-4 rounded border-slate-700 bg-slate-950 text-(--accent-primary) focus:ring-0">
                        <span>In stock only</span>
                    </label>
                </div>

                @if(count($attributeValues) > 0)
                    <div class="space-y-3 border-t border-slate-800/80 pt-4">
                        <span class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Attributes</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($attributeValues as $attr)
                                <button wire:click="$toggle('selectedAttributes.{{ $attr->id }}')"
                                    class="rounded-full border px-2.5 py-1.5 text-[10px] font-black uppercase tracking-wide transition {{ !empty($selectedAttributes[$attr->id]) ? 'border-(--accent-primary) bg-(--accent-primary)/15 text-(--accent-text)' : 'border-slate-800 bg-slate-950/70 text-slate-400 hover:text-white' }}">
                                    {{ $attr->value }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </aside>

        <main class="space-y-5">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($products as $product)
                        @php
                            $firstImg = $product->images->first()?->image;
                            $startingPrice = $product->variants->isNotEmpty()
                                ? $product->variants->map(fn ($variant) => (float) ($variant->price ?? $product->final_price))->min()
                                : (float) $product->final_price;
                        @endphp

                        <article class="group theme-card flex h-full flex-col justify-between rounded-[26px] border border-slate-800/80 p-3.5 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-slate-700 hover:shadow-2xl">
                            <div>
                                <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="relative block overflow-hidden rounded-2xl border border-slate-900 bg-slate-950">
                                    <div class="aspect-[4/5] w-full">
                                        @if($firstImg)
                                            <img src="{{ asset('storage/' . $firstImg) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @else
                                            <div class="flex h-full w-full flex-col items-center justify-center gap-2 text-sm font-bold uppercase tracking-[0.2em] text-slate-600">
                                                <x-heroicon-o-photo class="size-7" />
                                                <span>Image</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="absolute left-2.5 top-2.5 rounded-full bg-rose-500 px-2 py-1 text-[9px] font-black uppercase tracking-wide text-white">
                                            Sale
                                        </span>
                                    @endif

                                    @if(!$product->is_in_stock)
                                        <span class="absolute right-2.5 top-2.5 rounded-full border border-slate-700 bg-slate-950/85 px-2 py-1 text-[9px] font-bold uppercase tracking-wide text-slate-300">
                                            Sold out
                                        </span>
                                    @endif
                                </a>

                                <div class="mt-3 flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-black uppercase tracking-[0.18em] text-(--accent-text)">
                                        {{ $product->brand?->name ?? ($product->category?->name ?? 'Store item') }}
                                    </span>
                                    @if($product->is_in_stock)
                                        <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-0.5 text-[9px] font-black uppercase tracking-wide text-emerald-300">
                                            In stock
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-2 text-sm font-black text-white line-clamp-2">
                                    <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="hover:text-(--accent-text) transition-colors">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                            </div>

                            <div class="mt-4 border-t border-slate-800/80 pt-3">
                                <div class="flex items-end justify-between gap-3">
                                    <div>
                                        <p class="text-lg font-black text-white">{{ $product->variants->isNotEmpty() ? 'From ' : '' }}₹{{ number_format($startingPrice, 0) }}</p>
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <p class="text-[10px] text-slate-500 line-through">₹{{ number_format($product->price, 0) }}</p>
                                        @endif
                                    </div>

                                    @if($product->variants->isNotEmpty())
                                        <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="theme-btn rounded-xl px-3.5 py-2 text-[11px] font-black uppercase tracking-wide">
                                            Choose options
                                        </a>
                                    @else
                                        <button wire:click="add({{ $product->id }})"
                                            wire:loading.attr="disabled"
                                            @disabled(!$product->is_in_stock)
                                            class="theme-btn rounded-xl px-3.5 py-2 text-[11px] font-black uppercase tracking-wide disabled:cursor-not-allowed disabled:opacity-40">
                                            <span wire:loading.remove wire:target="add({{ $product->id }})">
                                                {{ $product->is_in_stock ? 'Add +' : 'Out' }}
                                            </span>
                                            <span wire:loading wire:target="add({{ $product->id }})">
                                                Adding...
                                            </span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($products->hasPages())
                    <div class="pt-2">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="theme-card rounded-[28px] border border-slate-800/80 p-12 text-center shadow-xl">
                    <p class="text-3xl">🔎</p>
                    <h3 class="mt-4 text-lg font-black text-white">No products found</h3>
                    <p class="mt-2 text-sm text-slate-400">Try changing your filters or searching with another keyword.</p>
                </div>
            @endif
        </main>
    </div>
</div>