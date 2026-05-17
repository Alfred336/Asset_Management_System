<?php

namespace App\Notifications;

use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TechnicianStatusUpdateReminder extends Notification
{
    use Queueable;

    public function __construct(public Device $device) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Device Status Update Required')
            ->line("Please update the status for device: {$this->device->asset_tag}")
            ->line("Model: {$this->device->model}")
            ->line("Company: {$this->device->company->name}")
            ->action('Update Status', url(route('technician.dashboard')))
            ->line('Thank you for keeping our asset records up to date!');
    }

    public function toArray($notifiable): array
    {
        return [
            'device_id' => $this->device->id,
            'device_asset_tag' => $this->device->asset_tag,
            'message' => "Please update the status for device {$this->device->asset_tag}",
            'url' => route('technician.dashboard'),
        ];
    }
}
