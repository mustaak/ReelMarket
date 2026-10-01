<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'status', 'type'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'type' => 'string',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->type === 'super_admin' || $this->hasRole('Super Admin');
    }

    public function isAdmin(): bool
    {
        return $this->type === 'admin' || $this->hasRole('Admin');
    }

    public function isUser(): bool
    {
        return $this->type === 'user' || $this->hasRole('User');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function acceptedFollowers(): BelongsToMany
    {
        return $this->followers()->wherePivot('status', 'accepted');
    }

    public function acceptedFollowing(): BelongsToMany
    {
        return $this->following()->wherePivot('status', 'accepted');
    }

    public function sentFollows(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function receivedFollows(): HasMany
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function reels(): HasMany
    {
        return $this->hasMany(Reel::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

    public function isFollowing(User $user): bool
    {
        return $this->acceptedFollowing()
            ->whereKey($user->id)
            ->exists();
    }
}
