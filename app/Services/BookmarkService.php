<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BookmarkService
{
    public function toggle(User $user, Model $content): bool
    {
        $bookmark = Bookmark::query()
            ->where('user_id', $user->id)
            ->where('bookmarkable_type', $content->getMorphClass())
            ->where('bookmarkable_id', $content->getKey())
            ->first();

        if ($bookmark) {
            $bookmark->delete();

            return false;
        }

        Bookmark::query()->create([
            'user_id' => $user->id,
            'bookmarkable_type' => $content->getMorphClass(),
            'bookmarkable_id' => $content->getKey(),
        ]);

        return true;
    }
}
