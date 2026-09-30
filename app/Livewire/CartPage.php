<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CartPage extends Component
{
    /**
     * Quantity Increment / Decrement
     */
    public function updateQuantity(CartService $cartService, int $productId, int $change)
    {
        $product = Product::find($productId);

        if (! $product) {
            $cartService->remove($productId);
            $this->dispatch('cartUpdated');

            return;
        }

        $currentQty = $cartService->quantities()[$productId] ?? 0;
        $newQty = $currentQty + $change;

        // Service se quantity set karein
        $message = $cartService->set($product, $newQty);

        if ($message) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => $message]);
        } else {
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Cart updated successfully!']);
        }

        $this->dispatch('cartUpdated');
    }

    /**
     * Item Remove
     */
    public function removeItem(CartService $cartService, int $productId)
    {
        $cartService->remove($productId);

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Item removed from cart']);
    }

    /**
     * Clear Whole Cart
     */
    public function clearCart(CartService $cartService)
    {
        $cartService->clear();

        $this->dispatch('cartUpdated');
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
            ->where('stock_quantity', '>', 0)
            ->whereNotIn('id', $cartItems->pluck('id')->toArray())
            ->orderBy('price', 'asc')
            ->take(3)
            ->get();

        // dd($cartItems);

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
