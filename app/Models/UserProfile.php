<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = ['user_id', 'bio', 'cover_photo', 'profile_picture', 'is_public'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
