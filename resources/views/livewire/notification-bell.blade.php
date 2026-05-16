<?php

use Livewire\Volt\Component;

new class extends Component
{
    public $type = 'navbar'; // 'navbar' or 'sidebar'

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }
};
?>

<flux:dropdown>
    @if($type === 'navbar')
        <flux:navbar.item icon="bell" badge="{{ auth()->user()->unreadNotifications->count() ?: null }}" />
    @else
        <flux:sidebar.item icon="bell" badge="{{ auth()->user()->unreadNotifications->count() ?: null }}">
            {{ __('Notifications') }}
        </flux:sidebar.item>
    @endif

    <flux:menu class="w-80">
        <flux:menu.header>{{ __('Notifications') }}</flux:menu.header>

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
