<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $open = false;

    public ?string $notice = null;

    #[On('open-cart')]
    public function open(?string $notice = null): void
    {
        $this->notice = $notice;
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->notice = null;
    }

    public function increment(int $productId, CartService $cart, ?int $variantId = null): void
    {
        $this->change($productId, 1, $cart, $variantId);
    }

    public function decrement(int $productId, CartService $cart, ?int $variantId = null): void
    {
        $this->change($productId, -1, $cart, $variantId);
    }

    public function remove(int $productId, CartService $cart, ?int $variantId = null): void
    {
        $cart->remove($productId, $variantId);

        $this->notice = null;
        $this->dispatch('cart-updated')->to(CartCount::class);

        $this->dispatch('notify',
            type: 'success',
            message: 'successfully removed your product in cart! 🎉'
        );
    }

    private function change(int $productId, int $delta, CartService $cart, ?int $variantId): void
    {
        $lineKey = $variantId ? CartService::variantLineKey($variantId) : $productId;
        $current = $cart->quantities()[$lineKey] ?? null;
        $product = Product::find($productId);
        $variant = $variantId
            ? ProductVariant::query()->where('product_id', $productId)->find($variantId)
            : null;
        $notificationType = 'success';

        if ($current === null || ! $product || ($variantId && ! $variant)) {
            $cart->remove($productId, $variantId);
            $this->notice = 'This product is no longer available.';
            $notificationType = 'warning';
        } else {
            $this->notice = $cart->set($product, $current + $delta, $variant);
        }

        $this->dispatch('cart-updated')->to(CartCount::class);

        $this->dispatch(
            'notify',
            type: $notificationType,
            message: $this->notice ?? ($product?->name ?? 'Cart item').' updated in your cart.',
        );
    }

    public function render()
    {
        // The cart is only queried while the drawer is open
        $items = $this->open ? app(CartService::class)->items() : collect();

        return view('livewire.cart-drawer', [
            'items' => $items,
            'subtotal' => $items->sum('total'),
            'count' => $items->sum('qty'),
        ]);
    }
}
