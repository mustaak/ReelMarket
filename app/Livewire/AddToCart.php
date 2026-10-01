<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AddToCart extends Component
{
    #[Locked]
    public int $productId;

    #[Locked]
    public int $max = 0;

    #[Locked]
    public ?int $variantId = null;

    public function mount(int $productId, CartService $cart, ?int $variantId = null): void
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId
            ? ProductVariant::query()->where('product_id', $product->id)->findOrFail($variantId)
            : null;

        $this->productId = $product->id;
        $this->variantId = $variant?->id;
        $this->max = $cart->limitFor($product, $variant);
    }

    public function add(int $quantity, CartService $cart): void
    {
        $product = Product::findOrFail($this->productId);
        $variant = $this->variantId
            ? ProductVariant::query()->where('product_id', $product->id)->findOrFail($this->variantId)
            : null;
        $notice = $cart->add($product, $quantity, $variant);

        // Cart Drawer aur Cart Count Sync
        $this->dispatch('cart-updated')->to(CartCount::class);
        $this->dispatch('open-cart', notice: $notice)->to(CartDrawer::class);

        $this->dispatch(
            'notify',
            type: $notice ? 'warning' : 'success',
            message: $notice ?? "{$product->name} successfully added to your cart!",
        );
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
