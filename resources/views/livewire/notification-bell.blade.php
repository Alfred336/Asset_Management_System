<?php

use Livewire\Component;

new class extends Component
{
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }
};
?>

<flux:dropdown>
    <flux:button icon="bell" variant="ghost" size="sm">
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-medium text-white">{{ auth()->user()->unreadNotifications->count() }}</span>
        @endif
    </flux:button>

    <flux:menu class="w-80">
        <flux:menu.heading>{{ __('Notifications') }}</flux:menu.heading>

        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->unreadNotifications->take(10) as $notification)
                <flux:menu.item class="flex flex-col items-start gap-1">
                    <flux:text size="sm" class="font-medium">{{ $notification->data['message'] ?? 'New Notification' }}</flux:text>
                    <flux:text size="xs" color="zinc">{{ $notification->created_at->diffForHumans() }}</flux:text>
                </flux:menu.item>
            @empty
                <flux:menu.item class="text-zinc-500">{{ __('No new notifications') }}</flux:menu.item>
            @endforelse
        </div>

        @if(auth()->user()->unreadNotifications->count() > 0)
            <flux:menu.separator />
            <flux:menu.item wire:click="markAllAsRead">{{ __('Mark all as read') }}</flux:menu.item>
        @endif
    </flux:menu>
</flux:dropdown>
