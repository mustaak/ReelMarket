<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class NotificationsPage extends Component
{
    public function openNotification(string $notificationId): void
    {
        $notification = $this->currentUser()->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();
        $notification->markAsRead();

        $this->redirect($notification->data['url'] ?? route('home'));
    }

    public function markAllAsRead(): void
    {
        $this->currentUser()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render(): View
    {
        $user = $this->currentUser();

        return view('livewire.notifications-page', [
            'notifications' => $user->notifications()->latest()->paginate(25),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    private function currentUser(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
