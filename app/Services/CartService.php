<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart';

    /** Most units of one product allowed in a single order. */
    public const MAX_PER_LINE = 10;

    /** @return array<int|string, int> product or variant line key => quantity */
    public function quantities(): array
    {
        $clean = [];

        foreach ((array) session()->get(self::SESSION_KEY, []) as $key => $qty) {
            if ((int) $qty > 0) {
                $key = ctype_digit((string) $key) ? (int) $key : (string) $key;

                if (is_int($key) || self::variantIdFromLineKey($key) !== null) {
                    $clean[$key] = (int) $qty;
                }
            }
        }

        return $clean;
    }

    public static function variantLineKey(int $variantId): string
    {
        return 'variant:'.$variantId;
    }

    public static function variantIdFromLineKey(int|string $lineKey): ?int
    {
        $lineKey = (string) $lineKey;

        if (! str_starts_with($lineKey, 'variant:')) {
            return null;
        }

        $variantId = substr($lineKey, strlen('variant:'));

        return ctype_digit($variantId) && (int) $variantId > 0 ? (int) $variantId : null;
    }

    /** How many units of this product can be bought right now (0 = not purchasable). */
    public function limitFor(Product $product, ?ProductVariant $variant = null): int
    {
        if ($product->status !== 'active' || $product->visibility !== 'visible') {
            return 0;
        }

        $hasVariants = $product->relationLoaded('variants')
            ? $product->variants->isNotEmpty()
            : $product->variants()->exists();

        if ($variant) {
            if ((int) $variant->product_id !== (int) $product->id) {
                return 0;
            }

            return min(self::MAX_PER_LINE, max(0, $variant->stock_quantity));
        }

        if ($hasVariants) {
            return 0;
        }

        return min(self::MAX_PER_LINE, max(0, (int) $product->stock_quantity));
    }

    /** Adds to the quantity already in the cart. Returns a message if the request was adjusted. */
    public function add(Product $product, int $qty = 1, ?ProductVariant $variant = null): ?string
    {
        $lineKey = $variant ? self::variantLineKey($variant->id) : $product->id;
        $current = $this->quantities()[$lineKey] ?? 0;

        return $this->set($product, $current + max(1, $qty), $variant);
    }

    /** Sets an exact quantity (0 removes the line). Returns a message if it was adjusted. */
    public function set(Product $product, int $qty, ?ProductVariant $variant = null): ?string
    {
        $limit = $this->limitFor($product, $variant);
        $lineKey = $variant ? self::variantLineKey($variant->id) : $product->id;

        if ($limit === 0) {
            $this->remove($product->id, $variant?->id);

            return 'Sorry, this product is not available right now.';
        }

        if ($qty < 1) {
            $this->remove($product->id, $variant?->id);

            return null;
        }

        $message = null;

        if ($qty > $limit) {
            $qty = $limit;
            $stockQuantity = $variant?->stock_quantity ?? $product->stock_quantity;
            $message = $stockQuantity < self::MAX_PER_LINE
                ? "Only {$limit} left in stock."
                : 'You can order up to '.self::MAX_PER_LINE.' of this item at a time.';
        }

        $cart = $this->quantities();
        $cart[$lineKey] = $qty;
        session()->put(self::SESSION_KEY, $cart);

        return $message;
    }

    public function remove(int $productId, ?int $variantId = null): void
    {
        $cart = $this->quantities();
        $lineKey = $variantId ? self::variantLineKey($variantId) : $productId;
        unset($cart[$lineKey]);
        session()->put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /** Cart lines built from live product data. Products that are gone or unavailable are dropped. */
    public function items(): Collection
    {
        $quantities = $this->quantities();

        if ($quantities === []) {
            return collect();
        }

        $variantIds = [];
        $productIds = [];

        foreach (array_keys($quantities) as $lineKey) {
            $variantId = self::variantIdFromLineKey($lineKey);

            if ($variantId) {
                $variantIds[] = $variantId;
            } elseif (is_int($lineKey)) {
                $productIds[] = $lineKey;
            }
        }

        $variants = ProductVariant::query()
            ->whereKey($variantIds)
            ->get()
            ->keyBy('id');

        $productIds = array_merge($productIds, $variants->pluck('product_id')->map(fn ($id) => (int) $id)->all());

        $products = Product::query()
            ->whereIn('id', array_unique($productIds))
            ->where('status', 'active')
            ->where('visibility', 'visible')
            ->with(['images' => fn ($q) => $q->orderBy('sort_order'), 'variants'])
            ->get()
            ->keyBy('id');

        $healed = [];
        $lines = collect();

        foreach ($quantities as $lineKey => $qty) {
            $variantId = self::variantIdFromLineKey($lineKey);
            $variant = $variantId ? $variants->get($variantId) : null;
            $productId = $variant?->product_id ?? (is_int($lineKey) ? $lineKey : null);
            $product = $productId ? $products->get($productId) : null;

            if (! $product || ($variantId && ! $variant)) {
                continue;
            }

            $limit = $this->limitFor($product, $variant);

            if ($limit === 0) {
                continue;
            }

            $qty = min($qty, $limit);
            $unit = (float) ($variant?->price ?? $product->final_price);
            $healed[$lineKey] = $qty;

            $lines->push([
                'id' => $product->id,
                'variant_id' => $variant?->id,
                'cart_key' => $lineKey,
                'slug' => $product->slug,
                'name' => $product->name,
                'variant_label' => $variant?->label,
                'variant_options' => $variant?->options ?? [],
                'sku' => $variant?->sku ?? $product->sku,
                'image' => $product->images->first()?->image,
                'qty' => $qty,
                'max' => $limit,
                'unit' => $unit,
                'total' => round($unit * $qty, 2),
            ]);
        }

        if ($healed !== $quantities) {
            session()->put(self::SESSION_KEY, $healed);
        }

        return $lines;
    }

    public function count(): int
    {
        return (int) $this->items()->sum('qty');
    }
}
