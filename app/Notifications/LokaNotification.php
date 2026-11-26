<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LokaNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param string $category 'claim'|'listing'|'volunteer'|'campaign'|'system'
     * @param string $icon 'shopping-bag'|'check-circle'|'clock'|'sparkles'|'heart'|'bell'
     * @param string|null $actionUrl
     * @param string|null $actionLabel
     * @param array $meta
     */
    public function __construct(
        public string $title,
        public string $message,
        public string $category = 'system',
        public string $icon = 'bell',
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
        public array $meta = []
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

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => $this->category,
            'icon' => $this->icon,
            'action_url' => $this->actionUrl,
            'action_label' => $this->actionLabel,
            'meta' => $this->meta,
        ];
    }
}
