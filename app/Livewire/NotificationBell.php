<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();

        $this->dispatch('notifications-read');
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
