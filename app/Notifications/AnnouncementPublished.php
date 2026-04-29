<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AnnouncementPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Announcement $announcement,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'hackathon_id' => $this->announcement->hackathon_id,
            'title' => $this->announcement->title,
            'body' => str()->limit(strip_tags($this->announcement->body), 100),
            'hackathon_title' => $this->announcement->hackathon->title,
        ];
    }
}
