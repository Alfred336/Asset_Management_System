<div>
    <x-layouts::auth :title="__('Verify Email')">
        <div class="flex flex-col gap-6">
            <x-auth-header
                :title="__('Check your email')"
                :description="__('We sent a 6-digit code to your email address. Enter it below to verify your account.')"
            />

            @if (session('status'))
                <flux:callout variant="success" icon="check-circle">{{ session('status') }}</flux:callout>
            @endif

            <form wire:submit="verify" class="flex flex-col gap-4">
                <flux:input
                    wire:model="code"
                    label="{{ __('Verification Code') }}"
                    placeholder="000000"
                    maxlength="6"
                    autofocus
                    required
                />
                @error('code')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Verify Email') }}
                </flux:button>
            </form>

            <div class="flex flex-col items-center gap-2">
                <flux:button wire:click="sendCode" variant="ghost" class="text-sm">
                    {{ __('Resend code') }}
                </flux:button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button variant="ghost" type="submit" class="text-sm">{{ __('Log out') }}</flux:button>
                </form>
            </div>
        </div>
    </x-layouts::auth>
</div>
