<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UnreadNotificationCount extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        $unreadCount = $user instanceof User ? $user->unreadNotifications()->count() : 0;

        return view('livewire.unread-notification-count', compact('unreadCount'));
    }
}
