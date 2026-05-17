<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyDeviceReportNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $pdf,
        private readonly int $totalDevices,
        private readonly int $offlineDevices,
        private readonly int $formattedDevices,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Weekly Device Report')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Attached is the weekly device report for '.now()->format('Y-m-d').'.')
            ->line('Total devices: '.$this->totalDevices)
            ->line('Offline devices: '.$this->offlineDevices)
            ->line('Formatted devices: '.$this->formattedDevices)
            ->attachData(
                $this->pdf,
                'weekly-device-report-'.now()->format('Y-m-d').'.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
