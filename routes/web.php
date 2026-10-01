<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use App\Livewire\CartPage;
use App\Livewire\CheckoutPage;
use App\Livewire\CreateReelPage;
use App\Livewire\HomeFeed;
use App\Livewire\MessagesPage;
use App\Livewire\NotificationsPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ReelsPage;
use App\Livewire\SavedContentPage;
use App\Livewire\ShopPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeFeed::class)->name('home');
Route::get('/shop', ShopPage::class)->name('shop.index');
Route::get('/product/{slug}', ProductDetailPage::class)->name('product.detail');
Route::get('/cart', CartPage::class)->name('cart.index');
Route::get('/reels', ReelsPage::class)->name('reels.index');
Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('users.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/reels/create', CreateReelPage::class)->name('reels.create');
    Route::get('/notifications', NotificationsPage::class)->name('notifications.index');
    Route::get('/saved', SavedContentPage::class)->name('saved.index');
    Route::get('/messages', MessagesPage::class)->name('messages.index');
    Route::get('/checkout', CheckoutPage::class)->name('checkout.index');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::patch('/profile/privacy', [AuthController::class, 'updateProfilePrivacy'])->name('profile.privacy.update');
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow.toggle');
    Route::post('/follow-requests/{follow}/accept', [FollowController::class, 'accept'])->name('follow-requests.accept');
    Route::post('/follow-requests/{follow}/reject', [FollowController::class, 'reject'])->name('follow-requests.reject');
    Route::get('/posts/create', [AuthController::class, 'createPost'])->name('posts.create');
    Route::post('/posts', [AuthController::class, 'storePost'])->name('posts.store');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
