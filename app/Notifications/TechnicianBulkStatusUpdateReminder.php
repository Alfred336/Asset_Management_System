<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class TechnicianBulkStatusUpdateReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Collection $devices) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->devices->count();
        $mail = (new MailMessage)
            ->subject('Action Required: Stale Device Status Updates')
            ->greeting("Hello {$notifiable->name},")
            ->line("There are {$count} devices that haven't had a status update in over 7 days.")
            ->line('Regular status updates are crucial for accurate asset tracking.');

        foreach ($this->devices->take(10) as $device) {
            $mail->line("• {$device->asset_tag} ({$device->model}) - Last Updated: ".($device->updated_at?->diffForHumans() ?? 'Never'));
        }

        if ($count > 10) {
            $mail->line('...and '.($count - 10).' more devices.');
        }

        return $mail
            ->action('Update Device Statuses', route('technician.dashboard'))
            ->line('Please review and update these devices at your earliest convenience.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'count' => $this->devices->count(),
            'message' => "You have {$this->devices->count()} devices requiring status updates.",
            'type' => 'status_reminder',
            'action_url' => route('technician.dashboard'),
        ];
    }
}
