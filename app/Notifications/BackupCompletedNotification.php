<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupCompletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly ?string $backupFile = null,
        private readonly string $output = ''
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('✅ Database Backup Completed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your daily database backup has been completed successfully.')
            ->line('**Backup Details:**')
            ->line('Time: ' . now()->format('Y-m-d H:i:s'))
            ->line('Status: Completed Successfully');

        if ($this->backupFile) {
            $message->line('Location: ' . $this->backupFile);
        }

        $message->line('The backup has been securely stored and is ready for recovery if needed.')
            ->action('View Documentation', route('home'))
            ->line('Thank you for using Asset Management System!');

        return $message;
    }
}
