<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UnreadMessageCount extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        $unreadMessageCount = 0;

        if ($user instanceof User) {
            $unreadMessageCount = Message::query()
                ->whereHas('conversation.users', fn (Builder $query) => $query->whereKey($user->id))
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        return view('livewire.unread-message-count', compact('unreadMessageCount'));
    }
}
