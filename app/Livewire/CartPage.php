<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\CartService;
use App\Models\Product;

#[Layout('components.layouts.app')]
class CartPage extends Component
{
    public $voucherCode = '';
    public $appliedDiscount = 0;
    public $voucherError = '';
    public $voucherSuccess = '';

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

    /**
     * Apply Coupon Code
     */
    public function applyVoucher()
    {
        $this->voucherError = '';
        $this->voucherSuccess = '';

        if (strtoupper(trim($this->voucherCode)) === 'SAVE100') {
            $this->appliedDiscount = 100;
            $this->voucherSuccess = 'Voucher code applied successfully!';
        } else {
            $this->voucherError = 'Invalid voucher code. Try SAVE100';
        }
    }

    public function render(CartService $cartService)
    {
        $cartItems = $cartService->items();
        $subtotal = $cartItems->sum('total');
        $pickupFee = $cartItems->isNotEmpty() ? 99 : 0;
        $tax = $subtotal * 0.10; // 10% Tax example
        $total = max(0, ($subtotal + $pickupFee + $tax) - $this->appliedDiscount);

        $lowestPriceProducts = Product::query()
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->whereNotIn('id', $cartItems->pluck('id')->toArray())
            ->orderBy('price', 'asc')
            ->take(3)
            ->get();

        //dd($cartItems);

        return view('livewire.cart-page', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'pickupFee' => $pickupFee,
            'tax' => $tax,
            'total' => $total,
            'lowestPriceProducts' => $lowestPriceProducts,
        ]);
    }
}