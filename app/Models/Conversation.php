<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'direct_pair_key',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'conversation_user'
        )->withPivot('accepted_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)
            ->latest();
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)
            ->latestOfMany();
    }
}