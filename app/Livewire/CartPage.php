<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CartPage extends Component
{
    /**
     * Quantity Increment / Decrement
     */
    public function updateQuantity(CartService $cartService, int $productId, int $change, ?int $variantId = null): void
    {
        $product = Product::find($productId);
        $variant = $variantId
            ? ProductVariant::query()->where('product_id', $productId)->find($variantId)
            : null;

        if (! $product || ($variantId && ! $variant)) {
            $cartService->remove($productId, $variantId);
            $this->dispatch('cart-updated');

            return;
        }

        $lineKey = $variant ? CartService::variantLineKey($variant->id) : $productId;
        $currentQty = $cartService->quantities()[$lineKey] ?? 0;
        $newQty = $currentQty + $change;

        $message = $cartService->set($product, $newQty, $variant);

        if ($message) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => $message]);
        } else {
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Cart updated successfully!']);
        }

        $this->dispatch('cart-updated');
    }

    /**
     * Item Remove
     */
    public function removeItem(CartService $cartService, int $productId, ?int $variantId = null): void
    {
        $cartService->remove($productId, $variantId);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Item removed from cart']);
    }

    /**
     * Clear Whole Cart
     */
    public function clearCart(CartService $cartService): void
    {
        $cartService->clear();

        $this->dispatch('cart-updated');
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Cart cleared']);
    }

    public function render(CartService $cartService)
    {
        $cartItems = $cartService->items();
        $subtotal = $cartItems->sum('total');
        $shippingFee = 0;
        $tax = 0;
        $total = $subtotal;

        $lowestPriceProducts = Product::query()
            ->where('status', 'active')
            ->where('visibility', 'visible')
            ->where(function ($query): void {
                $query->where('stock_quantity', '>', 0)
                    ->orWhereHas('variants', fn ($variants) => $variants->where('stock_quantity', '>', 0));
            })
            ->whereNotIn('id', $cartItems->pluck('id')->unique())
            ->with(['images', 'variants'])
            ->orderBy('price', 'asc')
            ->take(12)
            ->get()
            ->filter(fn (Product $product) => $product->is_in_stock)
            ->take(3)
            ->values();

        return view('livewire.cart-page', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'tax' => $tax,
            'total' => $total,
            'lowestPriceProducts' => $lowestPriceProducts,
        ]);
    }
}
