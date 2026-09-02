<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your personal identification and preferences')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="name" :label="__('Full Name')" type="text" required autofocus autocomplete="name" />
                <flux:input wire:model="email" :label="__('Email Address')" type="email" required autocomplete="email" />
            </div>

            @if ($this->hasUnverifiedEmail)
                <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30 text-amber-700 dark:text-amber-400 text-sm">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <flux:icon name="exclamation-triangle" class="size-4" />
                        {{ __('Verification Required') }}
                    </div>
                    <p class="text-xs">
                        {{ __('Your email address is unverified.') }}
                        <flux:link class="font-bold underline cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Resend verification email') }}
                        </flux:link>
                    </p>
                </div>
            @endif

            <flux:select wire:model="timezone" :label="__('Account Timezone')" searchable>
                @foreach($this->timezones as $tz)
                    <flux:select.option :value="$tz">{{ $tz }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex items-center gap-4 pt-4">
                <flux:button variant="primary" type="submit" class="shadow-sm">{{ __('Save Changes') }}</flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <div class="mt-12 pt-12 border-t border-zinc-200 dark:border-zinc-800">
                <livewire:settings.delete-user-form />
            </div>
        @endif
    </x-settings.layout>
</section>
