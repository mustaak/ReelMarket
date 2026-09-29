<?php

namespace App\Livewire;

use App\Models\Product;
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

    public function increment(int $productId, CartService $cart): void
    {
        $this->change($productId, 1, $cart);
    }

    public function decrement(int $productId, CartService $cart): void
    {
        $this->change($productId, -1, $cart);
    }

    public function remove(int $productId, CartService $cart): void
    {
        $cart->remove($productId);

        $this->notice = null;
        $this->dispatch('cart-updated')->to(CartCount::class);

         $this->dispatch('notify', 
            type: 'success', 
            message: "successfully removed your product in cart! 🎉"
        );
    }

    private function change(int $productId, int $delta, CartService $cart): void
    {
        $current = $cart->quantities()[$productId] ?? null;
        $product = Product::find($productId);

        if ($current === null || ! $product) {
            $cart->remove($productId);
        } else {
            $this->notice = $cart->set($product, $current + $delta);
        }

        $this->dispatch('cart-updated')->to(CartCount::class);

        $this->dispatch('notify', 
            type: 'success', 
            message: "{$product->title} successfully updated your cart! 🎉"
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