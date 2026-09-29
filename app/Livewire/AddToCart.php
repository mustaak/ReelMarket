<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AddToCart extends Component
{
    #[Locked]
    public int $productId;

    #[Locked]
    public int $max = 0;

    public function mount(int $productId, CartService $cart): void
    {
        $product = Product::findOrFail($productId);

        $this->productId = $product->id;
        $this->max = $cart->limitFor($product);
    }

    public function add(int $quantity, CartService $cart): void
    {
        $product = Product::findOrFail($this->productId);
        $notice = $cart->add($product, $quantity);

        // Cart Drawer aur Cart Count Sync
        $this->dispatch('cart-updated')->to(CartCount::class);
        $this->dispatch('open-cart', notice: $notice)->to(CartDrawer::class);

        $this->dispatch('notify', 
            type: 'success', 
            message: "{$product->title} successfully added to your cart! 🎉"
        );
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}