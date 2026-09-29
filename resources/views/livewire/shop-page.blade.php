<div class="w-full min-h-screen space-y-6">

    <!-- HEADER SEARCH & CATEGORY BAR -->
    <div class="theme-card border border-slate-800/80 rounded-2xl p-4 md:p-5 shadow-2xl space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Search Input -->
            <div class="relative w-full md:w-96">
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Search products, brands, SKU..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-800 text-white text-xs placeholder:text-slate-500 focus:outline-none focus:border-amber-500/50 transition-all shadow-inner" />
                <span class="absolute left-3.5 top-3 text-slate-500">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
            </div>

            <!-- Sorting Dropdown -->
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" onclick="Livewire.dispatch('open-cart')"
                                class="flex w-full items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white font-medium text-sm">
                    <span class="relative">
                        <x-heroicon-o-shopping-cart class="size-5" />
                        <livewire:cart-count />
                    </span>
                    <span>Cart</span>
                </button>
            </div>

        </div>

        <!-- Horizontal Category Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pt-2 [::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] border-t border-slate-800/60">
            <button wire:click="$set('selectedCategory', 'All')" 
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $selectedCategory === 'All' ? 'theme-btn text-black shadow-lg scale-105' : 'bg-slate-900/80 text-slate-400 hover:text-white border border-slate-800' }}">
                All Products
            </button>
            @foreach($categories as $category)
                <button wire:click="$set('selectedCategory', '{{ $category->slug ?? $category->id }}')" 
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $selectedCategory == ($category->slug ?? $category->id) ? 'theme-btn text-black shadow-lg scale-105' : 'bg-slate-900/80 text-slate-400 hover:text-white border border-slate-800' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- MAIN SECTION: SIDEBAR FILTERS + PRODUCT GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT FILTER SIDEBAR (3 Columns) -->
        <aside class="lg:col-span-3 theme-card border border-slate-800/80 rounded-2xl p-4 space-y-5 shadow-xl sticky top-6">
            
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <span class="text-xs font-extrabold text-white tracking-widest uppercase">FILTERS</span>
                <button wire:click="clearFilters" class="text-[11px] font-bold text-rose-400 hover:underline transition">
                    Reset
                </button>
            </div>

            <!-- BRANDS -->
            @if(count($brands) > 0)
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Brands</span>
                    <div class="space-y-1.5 max-h-40 overflow-y-auto no-scrollbar pr-1">
                        @foreach($brands as $brand)
                            <label class="flex items-center gap-2 text-xs text-slate-300 hover:text-white cursor-pointer transition">
                                <input type="checkbox" wire:model.live="selectedBrands" value="{{ $brand->id }}" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-0">
                                <span>{{ $brand->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- PRICE RANGE -->
            <div class="space-y-2 border-t border-slate-800/80 pt-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Price Range (₹)</span>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" wire:model.live.debounce.400ms="minPrice" placeholder="Min" class="w-full px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white placeholder:text-slate-600 focus:outline-none">
                    <input type="number" wire:model.live.debounce.400ms="maxPrice" placeholder="Max" class="w-full px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white placeholder:text-slate-600 focus:outline-none">
                </div>
            </div>

            <!-- IN STOCK ONLY -->
            <div class="border-t border-slate-800/80 pt-4">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-300 cursor-pointer">
                    <input type="checkbox" wire:model.live="inStockOnly" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-0">
                    <span>In Stock Only</span>
                </label>
            </div>

            <!-- ATTRIBUTES (COLOR / SIZE) -->
            @if(count($attributeValues) > 0)
                <div class="space-y-2 border-t border-slate-800/80 pt-4">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Attributes</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($attributeValues as $attr)
                            <button wire:click="$toggle('selectedAttributes.{{ $attr->id }}')" 
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-all {{ in_array($attr->id, $selectedAttributes) ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white' }}">
                                {{ $attr->value }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

        </aside>

        <!-- RIGHT PRODUCTS FEED (9 Columns) -->
        <main class="lg:col-span-9 space-y-5">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-5">
                    @foreach($products as $product)
                        @php
                            $firstImg = $product->images->first()?->image;
                        @endphp

                        <div class="group theme-card border border-slate-800/80 rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-300 hover:border-slate-700 shadow-md hover:shadow-2xl">
                            <div>
                                <!-- Image Container with Link -->
                                <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="block relative w-full aspect-[4/5] rounded-xl overflow-hidden bg-slate-950 mb-3 border border-slate-900 cursor-pointer">
                                    @if($firstImg)
                                        <img src="{{ asset('storage/' . $firstImg) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-600 bg-slate-950 font-bold text-xs">
                                            <svg class="size-8 mb-1 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>NO IMAGE</span>
                                        </div>
                                    @endif

                                    <!-- Sale Badge -->
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="absolute top-2.5 left-2.5 bg-rose-500 text-white text-[9px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider shadow">
                                            Sale
                                        </span>
                                    @endif

                                    <!-- Out of stock badge -->
                                    @if(!$product->is_in_stock)
                                        <span class="absolute top-2.5 right-2.5 bg-slate-950/90 text-slate-400 text-[9px] font-bold px-2 py-0.5 rounded-md border border-slate-800">
                                            Out of Stock
                                        </span>
                                    @endif
                                </a>

                                <!-- Brand / Subtitle -->
                                <div class="text-[10px] font-extrabold uppercase theme-text tracking-wider mb-0.5">
                                    {{ $product->brand?->name ?? ($product->category?->name ?? 'Store Item') }}
                                </div>

                                <!-- Product Title with Link -->
                                <h3 class="text-xs font-bold text-white group-hover:theme-text transition-colors line-clamp-1">
                                    <a href="{{ route('product.detail', $product->slug ?? $product->id) }}" class="hover:underline">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                            </div>

                            <!-- Price & Add Button -->
                            <div class="flex items-center justify-between mt-4 pt-2.5 border-t border-slate-800/80">
                                <div>
                                    <span class="text-sm font-black text-white">
                                        ₹{{ number_format($product->final_price, 0) }}
                                    </span>
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="text-[10px] text-slate-500 line-through block -mt-1">
                                            ₹{{ number_format($product->price, 0) }}
                                        </span>
                                    @endif
                                </div>

                               <button 
                                    wire:click="add({{ $product->id }})" 
                                    wire:loading.attr="disabled"
                                    @disabled(!$product->is_in_stock) 
                                    class="px-3.5 py-1.5 rounded-xl theme-btn text-black font-extrabold text-xs shadow hover:scale-105 transition disabled:opacity-40"
                                >
                                    <span wire:loading.remove wire:target="add({{ $product->id }})">
                                        {{ $product->is_in_stock ? 'Add +' : 'Out' }}
                                    </span>
                                    <span wire:loading wire:target="add({{ $product->id }})">
                                        Adding...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINATION -->
                <div class="pt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-20 theme-card rounded-2xl border border-slate-800">
                    <p class="text-slate-400 text-xs font-bold">No products found matching your filters.</p>
                </div>
            @endif
        </main>

    </div>

    <script>
    document.querySelectorAll('.no-scrollbar').forEach(el => {
        el.addEventListener('wheel', function (e) {
            if (e.deltaY !== 0) {
                e.preventDefault();
                this.scrollLeft += e.deltaY;
            }
        }, { passive: false });
    });
    </script>

</div>