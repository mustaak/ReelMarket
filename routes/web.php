<?php

use App\Http\Controllers\AuthController;
use App\Livewire\CartPage;
use App\Livewire\HomeFeed;
use App\Livewire\ProductDetailPage;
use App\Livewire\ReelsPage;
use App\Livewire\ShopPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeFeed::class)->name('home');
Route::get('/shop', ShopPage::class)->name('shop.index');
Route::get('/product/{slug}', ProductDetailPage::class)->name('product.detail');
Route::get('/cart', CartPage::class)->name('cart.index');
Route::get('/reels', ReelsPage::class)->name('reels.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/posts/create', [AuthController::class, 'createPost'])->name('posts.create');
    Route::post('/posts', [AuthController::class, 'storePost'])->name('posts.store');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
