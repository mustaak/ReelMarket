<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'content', 'visibility', 'status', 'image'];

    public function scopeVisibleTo(Builder $query, ?User $viewer): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $query) use ($viewer): void {
                $query->where(function (Builder $query): void {
                    $query->where('visibility', 'public')
                        ->whereDoesntHave('user.profile', fn (Builder $profileQuery) => $profileQuery->where('is_public', false));
                });

                if ($viewer) {
                    $query->orWhere(function (Builder $query) use ($viewer): void {
                        $query->whereIn('visibility', ['public', 'followers'])
                            ->where(function (Builder $query) use ($viewer): void {
                                $query->where('user_id', $viewer->getKey())
                                    ->orWhereHas('user.followers', function (Builder $followersQuery) use ($viewer): void {
                                        $followersQuery->whereKey($viewer->getKey())
                                            ->where('follows.status', 'accepted');
                                    });
                            });
                    });
                }
            });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
