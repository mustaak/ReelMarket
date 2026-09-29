<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Services\FilamentPermissionService;
use App\Models\Like;
use App\Models\Comment;
use App\Observers\LikeObserver;
use App\Observers\CommentObserver;
use App\Models\Follow;
use App\Observers\FollowObserver;
use App\Models\Post;
use App\Observers\PostObserver;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\View;


use App\Observers\UserObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Like::observe(LikeObserver::class);
        Comment::observe(CommentObserver::class);

        View::composer('layouts.app', function ($view) {
            $trendingProducts = Product::query()
                ->with('images')
                ->where('featured', 1)
                ->latest()
                ->take(5)
                ->get();

            $view->with('trendingProducts', $trendingProducts);
        });
    }
}
