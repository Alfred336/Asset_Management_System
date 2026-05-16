<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCode extends Notification
{
    public function __construct(public string $code) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Email Verification Code')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Use the code below to verify your email address. It expires in 10 minutes.')
            ->line('**' . $this->code . '**')
            ->line('If you did not create an account, no further action is required.');
    }
}
