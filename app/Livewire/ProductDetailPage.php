<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Product;
use App\Models\Reel;
use App\Services\ProductCatalogCache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProductDetailPage extends Component
{
    #[Locked]
    public Product $product;

    public ?int $selectedVariantId = null;

    protected ProductCatalogCache $productCatalogCache;

    public function boot(ProductCatalogCache $productCatalogCache): void
    {
        $this->productCatalogCache = $productCatalogCache;
    }

    public function mount(string $slug): void
    {
        $productData = $this->productCatalogCache->remember(
            'detail:'.hash('sha256', $slug),
            function () use ($slug): array {
                $productId = Product::query()
                    ->where('status', 'active')
                    ->where('visibility', 'visible')
                    ->where(function ($query) use ($slug) {
                        $query->where('slug', $slug);

                        // Allow links by id, but only when the value is purely numeric
                        if (ctype_digit($slug)) {
                            $query->orWhere('id', $slug);
                        }
                    })
                    ->value('id');

                return ['id' => $productId];
            },
        );

        abort_if($productData['id'] === null, 404);

        $this->product = Product::query()
            ->where('status', 'active')
            ->where('visibility', 'visible')
            ->with(['images', 'category', 'brand', 'attributeValues.attribute', 'variants'])
            ->findOrFail($productData['id']);

        $this->selectedVariantId = $this->product->variants
            ->first(fn ($variant) => $variant->stock_quantity > 0)?->id
            ?? $this->product->variants->first()?->id;
    }

    public function render()
    {
        $relatedProductData = $this->productCatalogCache->remember(
            'related:'.$this->product->id.':'.$this->product->category_id,
            function (): array {
                $base = Product::query()
                    ->where('status', 'active')
                    ->where('visibility', 'visible')
                    ->whereKeyNot($this->product->id);

                $relatedProductIds = (clone $base)
                    ->when($this->product->category_id, fn ($q) => $q->where('category_id', $this->product->category_id))
                    ->latest('id')
                    ->take(4)
                    ->pluck('id')
                    ->all();

                if (count($relatedProductIds) < 4) {
                    $relatedProductIds = array_merge(
                        $relatedProductIds,
                        (clone $base)
                            ->whereNotIn('id', $relatedProductIds)
                            ->latest('id')
                            ->take(4 - count($relatedProductIds))
                            ->pluck('id')
                            ->all(),
                    );
                }

                return ['ids' => $relatedProductIds];
            },
        );
        $relatedProductsById = Product::query()
            ->where('status', 'active')
            ->where('visibility', 'visible')
            ->with(['images', 'brand', 'category'])
            ->whereKey($relatedProductData['ids'])
            ->get()
            ->keyBy('id');
        $relatedProducts = collect($relatedProductData['ids'])
            ->map(fn ($productId) => $relatedProductsById->get($productId))
            ->filter()
            ->values();

        $seenIn = Post::where('product_id', $this->product->id)->where('status', 'published')->count()
            + Reel::where('product_id', $this->product->id)->where('status', 'published')->count();

        return view('livewire.product-detail-page', [
            'relatedProducts' => $relatedProducts,
            'seenIn' => $seenIn,
            'variants' => $this->product->variants,
            'selectedVariant' => $this->product->variants->firstWhere('id', $this->selectedVariantId),
        ]);
    }
}
