<?php

namespace App\Observers;

use App\Services\ProductCatalogCache;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;

class CatalogCacheObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private ProductCatalogCache $productCatalogCache) {}

    public function saved(Model $model): void
    {
        $this->productCatalogCache->invalidate();
    }

    public function deleted(Model $model): void
    {
        $this->productCatalogCache->invalidate();
    }
}
