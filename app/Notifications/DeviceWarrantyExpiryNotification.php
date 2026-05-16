<?php

namespace App\Notifications;

use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeviceWarrantyExpiryNotification extends Notification
{
    use Queueable;

    protected $device;

    /**
     * Create a new notification instance.
     */
    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Device Warranty Expiring Soon')
            ->line('The warranty for device '.$this->device->asset_tag.' ('.$this->device->model.') is expiring soon.')
            ->line('Expiry Date: '.$this->device->warranty_expiry->format('Y-m-d'))
            ->action('View Device', route('devices.index', ['search' => $this->device->asset_tag]))
            ->line('Please take necessary actions.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'device_id' => $this->device->id,
            'asset_tag' => $this->device->asset_tag,
            'model' => $this->device->model,
            'warranty_expiry' => $this->device->warranty_expiry->format('Y-m-d'),
            'message' => 'Warranty for '.$this->device->asset_tag.' expires on '.$this->device->warranty_expiry->format('Y-m-d'),
        ];
    }
}
