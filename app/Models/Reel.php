<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Reel extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'video_path', 'thumbnail', 'caption', 'status'];

    public function scopeVisibleTo(Builder $query, ?User $viewer): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $query) use ($viewer): void {
                $query->whereDoesntHave('user.profile', fn (Builder $profileQuery) => $profileQuery->where('is_public', false));

                if ($viewer) {
                    $query->orWhere('user_id', $viewer->getKey())
                        ->orWhereHas('user.followers', function (Builder $followersQuery) use ($viewer): void {
                            $followersQuery->whereKey($viewer->getKey())
                                ->where('follows.status', 'accepted');
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
}
