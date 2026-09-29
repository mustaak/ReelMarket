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

    $groupedAttributes = $product->attributeValues->groupBy(fn ($item) => $item->attribute->name ?? 'Option');
    $initialSelection = (object) $groupedAttributes->map(fn ($values) => $values->first()->value)->all();
    $metaLine = collect([$product->brand?->name, $product->category?->name])->filter()->implode(' • ');
@endphp

<div class="mx-auto w-full max-w-6xl space-y-6 md:space-y-8">
    <div x-data="{ active: 0, selected: @js($initialSelection) }"
         class="theme-card overflow-hidden rounded-[32px] border border-slate-800/80 shadow-2xl">
        <div class="grid grid-cols-1 gap-6 p-4 md:p-6 xl:grid-cols-[1.1fr_0.9fr] xl:p-8">
            <section class="space-y-4">
                <div class="relative overflow-hidden rounded-[28px] border border-slate-800/80 bg-slate-950">
                    <div class="aspect-[4/5] w-full">
                        @if($imageUrls->isNotEmpty())
                            <img src="{{ $imageUrls->first() }}"
                                 :src="@js($imageUrls)[active]"
                                 alt="{{ $product->name }}"
                                 class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-xs font-black uppercase tracking-[0.28em] text-slate-600">
                                No Image
                            </div>
                        @endif
                    </div>

                    @if($onSale)
                        <span class="absolute left-3 top-3 rounded-full bg-rose-500 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white">
                            {{ $discount }}% off
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
                                        ? 'border-(--accent-primary) ring-2 ring-(--accent-primary)/30 scale-[0.98]'
                                        : 'border-slate-800 opacity-75 hover:opacity-100'"
                                    class="aspect-square overflow-hidden rounded-2xl border bg-slate-950 transition-all">
                                <img src="{{ $url }}" alt="" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="flex flex-col justify-center">
                @if($metaLine)
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">{{ $metaLine }}</p>
                @endif

                <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">
                    {{ $product->name }}
                </h1>

                <div class="mt-5 space-y-2">
                    <div class="flex flex-wrap items-baseline gap-3">
                        <span class="text-3xl font-black text-white">{{ $money($product->final_price) }}</span>
                        @if($onSale)
                            <span class="text-sm text-slate-500 line-through">{{ $money($price) }}</span>
                            <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-emerald-300">
                                Save {{ $money($price - $sale) }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 text-sm font-semibold">
                        @if($stock <= 0)
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-rose-400">Out of stock</span>
                        @elseif($stock <= 5)
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-amber-400">Only {{ $stock }} left</span>
                        @else
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-emerald-400">In stock</span>
                        @endif
                    </div>
                </div>

                @if($product->short_description)
                    <p class="mt-5 text-sm leading-6 text-slate-300">{{ $product->short_description }}</p>
                @endif

                <div class="mt-6 space-y-5">
                    @foreach($groupedAttributes as $attrName => $values)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ $attrName }}</span>
                                <span class="text-xs text-slate-500" x-text="selected[@js($attrName)]"></span>
                            </div>

                            @if(strtolower($attrName) === 'color')
                                <div class="flex flex-wrap items-center gap-2.5">
                                    @foreach($values as $val)
                                        @php
                                            $raw = trim((string) ($val->color_code ?? $val->value));
                                            $colorCss = (preg_match('/^#[0-9a-fA-F]{3,8}$/', $raw) || ctype_alpha($raw)) ? $raw : null;
                                        @endphp
                                        <button type="button"
                                                @click="selected[@js($attrName)] = @js($val->value)"
                                                title="{{ $val->value }}"
                                                aria-label="{{ $attrName }}: {{ $val->value }}"
                                                @if($colorCss) style="background-color: {{ $colorCss }};" @endif
                                                :class="selected[@js($attrName)] === @js($val->value)
                                                    ? 'ring-2 ring-white ring-offset-2 ring-offset-slate-950 scale-110'
                                                    : 'opacity-80 hover:opacity-100'"
                                                class="h-8 w-8 rounded-full border border-slate-700 bg-slate-600 transition-all">
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-wrap items-center gap-2">
                                    @foreach($values as $val)
                                        <button type="button"
                                                @click="selected[@js($attrName)] = @js($val->value)"
                                                :class="selected[@js($attrName)] === @js($val->value)
                                                    ? 'theme-btn text-black shadow-lg'
                                                    : 'border border-slate-800 bg-slate-950/70 text-slate-300 hover:text-white'"
                                                class="rounded-full px-3 py-2 text-[11px] font-black uppercase tracking-wide transition">
                                            {{ $val->value }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    <livewire:add-to-cart :product-id="$product->id" />
                </div>

                @if($seenIn > 0)
                    <div class="mt-5 rounded-2xl border border-dashed border-slate-700 bg-slate-950/45 p-4">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Community</p>
                        <p class="mt-2 text-sm font-bold text-white">
                            Seen in {{ $seenIn }} {{ \Illuminate\Support\Str::plural('post', $seenIn) }} and reels
                        </p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    <section class="theme-card rounded-[28px] border border-slate-800/80 shadow-xl">
        <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 md:p-6">
            @if($product->description)
                <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 p-4">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Description</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-300">{!! $product->description !!}</p>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 p-4">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Details</h2>
                <dl class="mt-3 grid grid-cols-[120px_1fr] gap-y-3 text-sm">
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
            </div>
        </div>
    </section>

    <section class="space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">More like this</p>
                <h2 class="mt-1 text-xl font-black text-white">You may also like</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="text-xs font-black uppercase tracking-wide theme-text hover:underline">
                View all
            </a>
        </div>

        @if($relatedProducts->count() > 0)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach($relatedProducts as $related)
                    @php
                        $relImg = $related->images->first()?->image;
                        $relOnSale = (float) $related->sale_price > 0 && (float) $related->sale_price < (float) $related->price;
                    @endphp

                    <article class="group theme-card rounded-[24px] border border-slate-800/80 p-3 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-slate-700">
                        <a href="{{ route('product.detail', $related->slug ?? $related->id) }}" class="relative block overflow-hidden rounded-2xl border border-slate-900 bg-slate-950">
                            <div class="aspect-[4/5] w-full">
                                @if($relImg)
                                    <img src="{{ asset('storage/' . $relImg) }}" alt="{{ $related->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-[10px] font-black uppercase tracking-[0.22em] text-slate-600">
                                        Image
                                    </div>
                                @endif
                            </div>

                            @if($relOnSale)
                                <span class="absolute left-2.5 top-2.5 rounded-full bg-rose-500 px-2 py-1 text-[9px] font-black uppercase tracking-wide text-white">
                                    Sale
                                </span>
                            @endif
                        </a>

                        <div class="mt-3">
                            <p class="text-[9px] font-black uppercase tracking-[0.18em] text-(--accent-text)">
                                {{ $related->brand?->name ?? ($related->category?->name ?? 'Store item') }}
                            </p>
                            <h3 class="mt-1 text-sm font-black text-white line-clamp-2">
                                <a href="{{ route('product.detail', $related->slug ?? $related->id) }}" class="hover:text-(--accent-text) transition-colors">
                                    {{ $related->name }}
                                </a>
                            </h3>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-800/80 pt-3">
                            <div>
                                <span class="text-sm font-black text-white">{{ $money($related->final_price ?? $related->price) }}</span>
                                @if($relOnSale)
                                    <span class="block text-[10px] text-slate-500 line-through">{{ $money($related->price) }}</span>
                                @endif
                            </div>

                            <a href="{{ route('product.detail', $related->slug ?? $related->id) }}" class="rounded-full border border-slate-700 bg-slate-950/60 px-2.5 py-1.5 text-[10px] font-black uppercase tracking-wide text-slate-200 transition hover:text-white">
                                View
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="theme-card rounded-[24px] border border-slate-800/80 p-8 text-center text-sm text-slate-400">
                No related products found.
            </div>
        @endif
    </section>
</div>