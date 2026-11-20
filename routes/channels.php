<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return Conversation::query()
        ->whereKey($conversationId)
        ->whereHas('users', function ($query) use ($user) {
            $query
                ->whereKey($user->id)
                ->whereNotNull('conversation_user.accepted_at');
        })
        ->exists();
});
