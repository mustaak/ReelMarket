<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomeFeed;
use App\Livewire\ShopPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\CartPage;
use App\Livewire\ReelsPage;

Route::get('/', HomeFeed::class)->name('home');
Route::get('/shop', ShopPage::class)->name('shop.index');
Route::get('/product/{slug}', ProductDetailPage::class)->name('product.detail');
Route::get('/cart', CartPage::class)->name('cart.index');
Route::get('/reels', \App\Livewire\ReelsPage::class)->name('reels.index');
