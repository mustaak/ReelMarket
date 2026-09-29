<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Product;
use App\Models\Reel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProductDetailPage extends Component
{
    #[Locked]
    public Product $product;

    public function mount(string $slug): void
    {
        $this->product = Product::with(['images', 'category', 'brand', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->where('visibility', 'visible')
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug);

                // Allow links by id, but only when the value is purely numeric
                if (ctype_digit($slug)) {
                    $query->orWhere('id', $slug);
                }
            })
            ->firstOrFail();
    }

    public function render()
    {
        $base = Product::with(['images', 'brand', 'category'])
            ->where('status', 'active')
            ->where('visibility', 'visible')
            ->whereKeyNot($this->product->id);

        $relatedProducts = (clone $base)
            ->when($this->product->category_id, fn ($q) => $q->where('category_id', $this->product->category_id))
            ->latest('id')
            ->take(4)
            ->get();

        // Fill up to 4 with other products if the category has fewer
        if ($relatedProducts->count() < 4) {
            $relatedProducts = $relatedProducts->merge(
                (clone $base)
                    ->whereNotIn('id', $relatedProducts->pluck('id'))
                    ->latest('id')
                    ->take(4 - $relatedProducts->count())
                    ->get()
            );
        }

        $seenIn = Post::where('product_id', $this->product->id)->where('status', 'published')->count()
            + Reel::where('product_id', $this->product->id)->where('status', 'published')->count();

        return view('livewire.product-detail-page', [
            'relatedProducts' => $relatedProducts,
            'seenIn' => $seenIn,
        ]);
    }
}