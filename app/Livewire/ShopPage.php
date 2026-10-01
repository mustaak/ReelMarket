<?php

namespace App\Livewire;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ShopPage extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedCategory = 'All';

    public $selectedBrands = [];

    public $minPrice = null;

    public $maxPrice = null;

    public $inStockOnly = false;

    public $selectedAttributes = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => 'All'],
        'selectedBrands' => ['except' => []],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'inStockOnly' => ['except' => false],
        'selectedAttributes' => ['except' => []],
    ];

    public function updated($propertyName)
    {
        $this->resetPage();
    }

    public function add(int $productId, CartService $cart): void
    {
        $product = Product::findOrFail($productId);
        $notice = $cart->add($product, 1);

        $this->dispatch('cart-updated')->to(CartCount::class);
        $this->dispatch('open-cart', notice: $notice)->to(CartDrawer::class);

        $this->dispatch(
            'notify',
            type: $notice ? 'warning' : 'success',
            message: $notice ?? "{$product->name} successfully added to your cart!",
        );
    }

    public function clearFilters()
    {
        $this->reset(['selectedCategory', 'selectedBrands', 'minPrice', 'maxPrice', 'inStockOnly', 'selectedAttributes', 'search']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::active()->with(['images', 'category', 'brand', 'attributeValues', 'variants']);

        // 1. Search Query
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('short_description', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%')
                    ->orWhereHas('brand', fn ($brandQuery) => $brandQuery->where('name', 'like', '%'.$this->search.'%'));
            });
        }

        // 2. Category Filter (category_id or slug)
        if ($this->selectedCategory !== 'All') {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->selectedCategory)
                    ->orWhere('id', $this->selectedCategory);
            });
        }

        // 3. Brands Filter (brand_id column)
        if (! empty($this->selectedBrands)) {
            $query->whereIn('brand_id', $this->selectedBrands);
        }

        // 4. Price Filter (using sale_price or price)
        if ($this->minPrice) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$this->minPrice]);
        }
        if ($this->maxPrice) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$this->maxPrice]);
        }

        // 5. Stock Filter
        if ($this->inStockOnly) {
            $query->where('stock_quantity', '>', 0);
        }

        // 6. Attribute Filter (Color, Size via Pivot)
        if (! empty($this->selectedAttributes)) {
            $selectedAttributeIds = array_keys(array_filter($this->selectedAttributes));

            $query->whereHas('attributeValues', function ($q) use ($selectedAttributeIds) {
                $q->whereIn('attribute_values.id', $selectedAttributeIds);
            });
        }

        $products = $query->latest()->paginate(9);

        $categories = Category::all();
        $brands = Brand::all();

        // Dynamic Attribute Values (Color, Size etc.)
        $attributeValues = AttributeValue::all();

        return view('livewire.shop-page', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'attributeValues' => $attributeValues,
        ]);
    }
}
