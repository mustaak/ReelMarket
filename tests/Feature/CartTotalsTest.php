<?php

namespace Tests\Feature;

use App\Livewire\CartPage;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTotalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_subtotal_uses_line_total_without_double_counting(): void
    {
        $product = Product::create([
            'name' => 'Wireless Headphones',
            'sku' => 'WH-100',
            'slug' => 'wireless-headphones',
            'short_description' => 'Premium wireless sound.',
            'price' => 100.00,
            'sale_price' => 80.00,
            'stock_quantity' => 10,
            'status' => 'active',
            'visibility' => 'visible',
        ]);

        session()->put('cart', [$product->id => 2]);

        $view = (new CartPage())->render(app(CartService::class));

        $this->assertSame(160.0, (float) $view->getData()['subtotal']);
        $this->assertSame(275.0, (float) $view->getData()['total']);
    }
}
