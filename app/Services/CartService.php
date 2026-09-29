<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart';

    /** Most units of one product allowed in a single order. */
    public const MAX_PER_LINE = 10;

    /** @return array<int, int> product id => quantity */
    public function quantities(): array
    {
        $clean = [];

        foreach ((array) session()->get(self::SESSION_KEY, []) as $id => $qty) {
            if ((int) $qty > 0) {
                $clean[(int) $id] = (int) $qty;
            }
        }

        return $clean;
    }

    /** How many units of this product can be bought right now (0 = not purchasable). */
    public function limitFor(Product $product): int
    {
        if ($product->status !== 'active' || $product->visibility !== 'visible') {
            return 0;
        }

        return min(self::MAX_PER_LINE, max(0, (int) $product->stock_quantity));
    }

    /** Adds to the quantity already in the cart. Returns a message if the request was adjusted. */
    public function add(Product $product, int $qty = 1): ?string
    {
        $current = $this->quantities()[$product->id] ?? 0;

        return $this->set($product, $current + max(1, $qty));
    }

    /** Sets an exact quantity (0 removes the line). Returns a message if it was adjusted. */
    public function set(Product $product, int $qty): ?string
    {
        $limit = $this->limitFor($product);

        if ($limit === 0) {
            $this->remove($product->id);

            return 'Sorry, this product is not available right now.';
        }

        if ($qty < 1) {
            $this->remove($product->id);

            return null;
        }

        $message = null;

        if ($qty > $limit) {
            $qty = $limit;
            $message = $product->stock_quantity < self::MAX_PER_LINE
                ? "Only {$limit} left in stock."
                : 'You can order up to ' . self::MAX_PER_LINE . ' of this item at a time.';
        }

        $cart = $this->quantities();
        $cart[$product->id] = $qty;
        session()->put(self::SESSION_KEY, $cart);

        return $message;
    }

    public function remove(int $productId): void
    {
        $cart = $this->quantities();
        unset($cart[$productId]);
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

        $products = Product::query()
            ->whereIn('id', array_keys($quantities))
            ->where('status', 'active')
            ->where('visibility', 'visible')
            ->where('stock_quantity', '>', 0)
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
            ->get()
            ->keyBy('id');

        $healed = [];
        $lines = collect();

        foreach ($quantities as $id => $qty) {
            $product = $products->get($id);

            if (! $product) {
                continue;
            }

            $limit = $this->limitFor($product);
            $qty = min($qty, $limit);
            $unit = (float) $product->final_price;
            $healed[$id] = $qty;

            $lines->push([
                'id' => $id,
                'slug' => $product->slug,
                'name' => $product->name,
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