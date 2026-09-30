<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SocialActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $actor,
        public string $activity,
        public string $message,
        public string $url,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, int|string|null> */
    public function toDatabase(object $notifiable): array
    {
        return [
            'activity' => $this->activity,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'actor_photo' => $this->actor->profile?->profile_picture,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
