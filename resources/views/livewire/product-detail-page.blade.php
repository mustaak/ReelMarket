@php
    $imageUrls = $product->images
        ->pluck('image')
        ->filter()
        ->map(fn ($path) => asset('storage/' . $path))
        ->values();

    $price = (float) $product->price;
    $sale = (float) $product->sale_price;
    $onSale = $sale > 0 && $sale < $price;
    $discount = $onSale ? round((1 - $sale / $price) * 100) : 0;
    $stock = (int) $product->stock_quantity;

    $money = fn ($v) => '₹' . number_format($v, fmod((float) $v, 1) === 0.0 ? 0 : 2);

    // Attribute values grouped by attribute name (Color, Size, ...)
    $groupedAttributes = $product->attributeValues->groupBy(fn ($item) => $item->attribute->name ?? 'Option');

    // First value of every attribute is pre-selected
    $initialSelection = (object) $groupedAttributes->map(fn ($values) => $values->first()->value)->all();

    $metaLine = collect([$product->brand?->name, $product->category?->name])->filter()->implode(' • ');
@endphp

<div class="w-full max-w-4xl mx-auto p-4 md:p-8 space-y-12 theme-card">

    <!-- MAIN PRODUCT SECTION -->
    <div x-data="{ active: 0, selected: @js($initialSelection) }"
         class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start mb-4">

        <!-- LEFT COLUMN: MAIN IMAGE & THUMBNAILS -->
        <div class="md:col-span-6 space-y-4">
            <div class="relative w-full aspect-square rounded-2xl overflow-hidden theme-inner shadow-xl border border-slate-800/40">
                @if($imageUrls->isNotEmpty())
                    <img src="{{ $imageUrls->first() }}"
                         :src="@js($imageUrls)[active]"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-500 font-bold text-xs">
                        NO IMAGE
                    </div>
                @endif

                @if($onSale)
                    <span class="absolute top-3 left-3 rounded-full bg-rose-500 px-2.5 py-1 text-xs font-black text-white">
                        {{ $discount }}% OFF
                    </span>
                @endif
            </div>

            @if($imageUrls->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($imageUrls as $url)
                        <button type="button"
                                @click="active = {{ $loop->index }}"
                                aria-label="Show image {{ $loop->iteration }}"
                                :class="active === {{ $loop->index }}
                                    ? 'theme-border scale-95 shadow'
                                    : 'border-transparent opacity-70 hover:opacity-100'"
                                class="w-full aspect-square rounded-xl overflow-hidden border-2 transition-all">
                            <img src="{{ $url }}" alt="" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: PRODUCT DETAILS & OPTIONS -->
        <div class="md:col-span-6 space-y-5 text-white">

            @if($metaLine)
                <div class="text-xs font-semibold text-slate-400">{{ $metaLine }}</div>
            @endif

            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white leading-tight">
                {{ $product->name }}
            </h1>

            <!-- Price -->
            <div class="space-y-1">
                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                    <span class="text-3xl font-black text-white">{{ $money($product->final_price) }}</span>
                    @if($onSale)
                        <span class="text-sm font-semibold text-slate-500 line-through">{{ $money($price) }}</span>
                        <span class="rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-bold text-emerald-400">
                            {{ $discount }}% off
                        </span>
                    @endif
                </div>
                @if($onSale)
                    <p class="text-sm text-emerald-400">You save {{ $money($price - $sale) }}</p>
                @endif
            </div>

            <!-- Stock -->
            <p class="flex items-center gap-2 text-sm font-medium">
                @if($stock <= 0)
                    <span class="size-2 rounded-full bg-rose-500"></span><span class="text-rose-400">Out of stock</span>
                @elseif($stock <= 5)
                    <span class="size-2 rounded-full bg-amber-500"></span><span class="text-amber-400">Only {{ $stock }} left</span>
                @else
                    <span class="size-2 rounded-full bg-emerald-500"></span><span class="text-emerald-400">In stock</span>
                @endif
            </p>

            @if($product->short_description)
                <p class="text-sm leading-relaxed text-slate-300">{{ $product->short_description }}</p>
            @endif

            <!-- ATTRIBUTES (Color, Size, ...) -->
            @foreach($groupedAttributes as $attrName => $values)
                <div class="space-y-2 pt-1">
                    <span class="text-xs font-bold text-slate-200 block">
                        {{ $attrName }}:
                        <span class="font-normal text-slate-400" x-text="selected[@js($attrName)]"></span>
                    </span>

                    @if(strtolower($attrName) === 'color')
                        <div class="flex items-center gap-3">
                            @foreach($values as $val)
                                @php
                                    // Accept a hex code or a plain color word, nothing else
                                    $raw = trim((string) ($val->color_code ?? $val->value));
                                    $colorCss = (preg_match('/^#[0-9a-fA-F]{3,8}$/', $raw) || ctype_alpha($raw)) ? $raw : null;
                                @endphp
                                <button type="button"
                                        @click="selected[@js($attrName)] = @js($val->value)"
                                        title="{{ $val->value }}"
                                        aria-label="{{ $attrName }}: {{ $val->value }}"
                                        @if($colorCss) style="background-color: {{ $colorCss }};" @endif
                                        :class="selected[@js($attrName)] === @js($val->value)
                                            ? 'border-2 border-white ring-2 ring-slate-400 scale-110 shadow-lg'
                                            : 'border border-slate-700 hover:scale-105 opacity-80 hover:opacity-100'"
                                        class="size-7 rounded-full bg-slate-600 transition-all duration-200">
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center gap-2 flex-wrap">
                            @foreach($values as $val)
                                <button type="button"
                                        @click="selected[@js($attrName)] = @js($val->value)"
                                        :aria-pressed="selected[@js($attrName)] === @js($val->value)"
                                        :class="selected[@js($attrName)] === @js($val->value)
                                            ? 'bg-white text-black shadow-md scale-105'
                                            : 'bg-white/5 text-slate-300 border border-slate-700 hover:text-white'"
                                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200">
                                    {{ $val->value }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- ADD TO CART -->
            <div class="pt-3">
                <livewire:add-to-cart :product-id="$product->id" />
            </div>

            <!-- SOCIAL CALLOUT (only with a real count) -->
            @if($seenIn > 0)
                <div class="border border-dashed border-slate-700 rounded-2xl p-4 theme-inner space-y-0.5">
                    <div class="text-xs font-bold text-white">
                        Seen in {{ $seenIn }} {{ \Illuminate\Support\Str::plural('post', $seenIn) }} and reels
                    </div>
                    <div class="text-[11px] text-slate-400 font-medium">
                        See how real customers use it
                    </div>
                </div>
            @endif

            <!-- DESCRIPTION & DETAILS -->
            <div class="divide-y divide-slate-800/80 rounded-xl border border-slate-800/80">
                @if($product->description)
                    <details class="group" open>
                        <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-sm font-bold text-white [&::-webkit-details-marker]:hidden">
                            Description
                            <x-heroicon-o-chevron-down class="size-4 text-slate-400 transition group-open:rotate-180" />
                        </summary>
                        <div class="whitespace-pre-line px-4 pb-4 text-sm leading-relaxed text-slate-300">{!! $product->description !!}</div>
                    </details>
                @endif

                <details class="group">
                    <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-sm font-bold text-white [&::-webkit-details-marker]:hidden">
                        Product details
                        <x-heroicon-o-chevron-down class="size-4 text-slate-400 transition group-open:rotate-180" />
                    </summary>
                    <dl class="grid grid-cols-[7rem_1fr] gap-y-2 px-4 pb-4 text-sm">
                        <dt class="text-slate-400">SKU</dt>
                        <dd class="text-slate-200">{{ $product->sku }}</dd>
                        @if($product->brand)
                            <dt class="text-slate-400">Brand</dt>
                            <dd class="text-slate-200">{{ $product->brand->name }}</dd>
                        @endif
                        @if($product->category)
                            <dt class="text-slate-400">Category</dt>
                            <dd class="text-slate-200">{{ $product->category->name }}</dd>
                        @endif
                    </dl>
                </details>
            </div>
        </div>
    </div>

    <!-- RELATED PRODUCTS SECTION -->
    <div class="space-y-5 pt-8 border-t border-slate-800/80">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-white tracking-tight">You Might Also Like</h2>
                <p class="text-xs text-slate-400 font-medium">Handpicked products matching this style</p>
            </div>
            <a href="{{ route('shop.index') }}" class="theme-text text-xs font-bold hover:underline">
                View All &rarr;
            </a>
        </div>

        @if($relatedProducts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($relatedProducts as $related)
                    @php
                        $relImg = $related->images->first()?->image;
                        $relOnSale = (float) $related->sale_price > 0 && (float) $related->sale_price < (float) $related->price;
                    @endphp
                    <div wire:key="related-{{ $related->id }}"
                         class="group theme-card border border-slate-800/80 rounded-2xl p-3 flex flex-col justify-between transition-all duration-300 hover:border-slate-700 shadow-md">
                        <div>
                            <a href="{{ route('product.detail', $related->slug ?? $related->id) }}"
                               class="block relative w-full aspect-[4/5] rounded-xl overflow-hidden bg-slate-950 mb-3 border border-slate-900">
                                @if($relImg)
                                    <img src="{{ asset('storage/' . $relImg) }}" alt="{{ $related->name }}" loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600 bg-slate-950 font-bold text-xs">
                                        NO IMAGE
                                    </div>
                                @endif

                                @if($relOnSale)
                                    <span class="absolute top-2 left-2 bg-rose-500 text-white text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-wider">
                                        Sale
                                    </span>
                                @endif
                            </a>

                            <div class="theme-text text-[10px] font-extrabold uppercase tracking-wider mb-0.5">
                                {{ $related->brand?->name ?? ($related->category?->name ?? 'Store Item') }}
                            </div>

                            <h3 class="text-xs font-bold text-white group-hover:text-(--accent-text) transition-colors line-clamp-1">
                                <a href="{{ route('product.detail', $related->slug ?? $related->id) }}">
                                    {{ $related->name }}
                                </a>
                            </h3>
                        </div>

                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-800/80">
                            <div>
                                <span class="text-xs font-black text-white">{{ $money($related->final_price ?? $related->price) }}</span>
                                @if($relOnSale)
                                    <span class="text-[10px] text-slate-500 line-through block -mt-1">{{ $money($related->price) }}</span>
                                @endif
                            </div>

                            <a href="{{ route('product.detail', $related->slug ?? $related->id) }}"
                               class="px-2.5 py-1 rounded-lg bg-slate-900 text-slate-300 hover:text-white border border-slate-800 text-[11px] font-bold transition">
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 text-slate-500 text-xs font-bold">
                No related products found.
            </div>
        @endif
    </div>
</div>