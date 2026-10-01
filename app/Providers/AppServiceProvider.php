<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Observers\CommentObserver;
use App\Observers\FollowObserver;
use App\Observers\LikeObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Follow::observe(FollowObserver::class);

    }
}
