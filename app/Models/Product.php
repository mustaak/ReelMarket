<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'slug',
        'short_description', 'description',
        'price', 'sale_price', 'cost_price',
        'stock_quantity', 'status', 'visibility', 'featured',
        'brand_id', 'category_id', 'weight',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'featured' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values')
            ->using(ProductAttributeValue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('visibility', 'visible');
    }

    public function getFinalPriceAttribute(): float
    {
        $price = (float) $this->price;
        $salePrice = (float) $this->sale_price;

        return $salePrice > 0 && $salePrice < $price ? $salePrice : $price;
    }

    public function getIsInStockAttribute(): bool
    {
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            return $this->variants->contains(fn (ProductVariant $variant) => $variant->stock_quantity > 0);
        }

        if ($this->variants()->exists()) {
            return $this->variants()->where('stock_quantity', '>', 0)->exists();
        }

        return $this->stock_quantity > 0;
    }
}
