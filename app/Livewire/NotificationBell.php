<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        $this->dispatch('notifications-read');
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
