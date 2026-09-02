<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Security settings') }}</flux:heading>

    <x-settings.layout :heading="__('Account Security')" :subheading="__('Secure your account with a strong password and two-factor authentication')">
        <div class="space-y-12">
            {{-- Password Section --}}
            <div class="space-y-6">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <flux:icon name="lock-closed" class="size-4 text-zinc-600 dark:text-zinc-400" />
                    </div>
                    <flux:heading size="md">Update Password</flux:heading>
                </div>
                
                <form method="POST" wire:submit="updatePassword" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <flux:input
                            wire:model="current_password"
                            :label="__('Current Password')"
                            type="password"
                            required
                            autocomplete="current-password"
                            viewable
                        />
                    </div>
                    <flux:input
                        wire:model="password"
                        :label="__('New Password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                        viewable
                    />
                    <flux:input
                        wire:model="password_confirmation"
                        :label="__('Confirm New Password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                        viewable
                    />

                    <div class="col-span-full flex items-center gap-4">
                        <flux:button variant="primary" type="submit" data-test="update-password-button" class="shadow-sm">{{ __('Update Password') }}</flux:button>
                    </div>
                </form>
            </div>

            @if ($canManageTwoFactor)
                <div class="space-y-6 pt-8 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                            <flux:icon name="shield-check" class="size-4 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <flux:heading size="md">Two-Factor Authentication</flux:heading>
                    </div>
                    <flux:subheading>{{ __('Add an extra layer of security to your account') }}</flux:subheading>

                    <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                        @if ($twoFactorEnabled)
                            <div class="space-y-4">
                                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                    <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider mb-1">
                                        <flux:icon name="check-circle" class="size-3" />
                                        {{ __('2FA Enabled') }}
                                    </div>
                                    <flux:text size="sm">
                                        {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                                    </flux:text>
                                </div>

                                <div class="flex justify-start">
                                    <flux:button
                                        variant="danger"
                                        wire:click="disable"
                                    >
                                        {{ __('Disable 2FA') }}
                                    </flux:button>
                                </div>

                                <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800">
                                    <flux:text variant="subtle">
                                        {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                                    </flux:text>
                                </div>

                                <flux:button
                                    variant="primary"
                                    wire:click="enable"
                                    class="shadow-sm"
                                >
                                    {{ __('Enable 2FA') }}
                                </flux:button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        @if($canManageTwoFactor)
            <flux:modal
                name="two-factor-setup-modal"
                class="max-w-md md:min-w-md"
                @close="closeModal"
                wire:model="showModal"
            >
                <div class="space-y-6">
                    <div class="flex flex-col items-center space-y-4">
                        <div class="p-1 w-auto rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm">
                            <div class="p-3 rounded-full border border-zinc-200 dark:border-zinc-700 overflow-hidden bg-zinc-50 dark:bg-zinc-800 relative">
                                <div class="flex items-stretch absolute inset-0 w-full h-full divide-x [&>div]:flex-1 divide-zinc-200 dark:divide-zinc-700 justify-around opacity-30">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div></div>
                                    @endfor
                                </div>

                                <div class="flex flex-col items-stretch absolute w-full h-full divide-y [&>div]:flex-1 inset-0 divide-zinc-200 dark:divide-zinc-700 justify-around opacity-30">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div></div>
                                    @endfor
                                </div>

                                <flux:icon.qr-code class="relative z-20 dark:text-accent-foreground"/>
                            </div>
                        </div>

                        <div class="space-y-2 text-center">
                            <flux:heading size="lg">{{ $this->modalConfig['title'] }}</flux:heading>
                            <flux:text>{{ $this->modalConfig['description'] }}</flux:text>
                        </div>
                    </div>

                    @if ($showVerificationStep)
                        <div class="space-y-6">
                            <div
                                class="flex flex-col items-center space-y-3 justify-center"
                                x-data
                                x-init="$nextTick(() => $el.querySelector('input')?.focus())"
                            >
                                <flux:otp
                                    name="code"
                                    wire:model="code"
                                    length="6"
                                    label="Verification Code"
                                    class="mx-auto"
                                />
                            </div>

                            <div class="flex items-center space-x-3">
                                <flux:button
                                    variant="outline"
                                    class="flex-1"
                                    wire:click="resetVerification"
                                >
                                    {{ __('Back') }}
                                </flux:button>

                                <flux:button
                                    variant="primary"
                                    class="flex-1"
                                    wire:click="confirmTwoFactor"
                                    x-bind:disabled="$wire.code.length < 6"
                                >
                                    {{ __('Confirm') }}
                                </flux:button>
                            </div>
                        </div>
                    @else
                        @error('setupData')
                            <flux:callout variant="danger" icon="x-circle" heading="{{ $message }}"/>
                        @enderror

                        <div class="flex justify-center">
                            <div class="relative w-64 overflow-hidden border rounded-lg border-zinc-200 dark:border-zinc-700 aspect-square">
                                @empty($qrCodeSvg)
                                    <div class="absolute inset-0 flex items-center justify-center bg-white dark:bg-zinc-700 animate-pulse">
                                        <flux:icon.loading/>
                                    </div>
                                @else
                                <div x-data class="flex items-center justify-center h-full p-4">
                                    <div
                                        class="bg-white p-3 rounded"
                                        :style="($flux.appearance === 'dark' || ($flux.appearance === 'system' && $flux.dark)) ? 'filter: invert(1) brightness(1.5)' : ''"
                                    >
                                            {!! $qrCodeSvg !!}
                                        </div>
                                    </div>
                                @endempty
                            </div>
                        </div>

                        <div>
                            <flux:button
                                :disabled="$errors->has('setupData')"
                                variant="primary"
                                class="w-full"
                                wire:click="showVerificationIfNecessary"
                            >
                                {{ $this->modalConfig['buttonText'] }}
                            </flux:button>
                        </div>

                        <div class="space-y-4">
                            <div class="relative flex items-center justify-center w-full">
                                <div class="absolute inset-0 w-full h-px top-1/2 bg-zinc-200 dark:bg-zinc-600"></div>
                                <span class="relative px-2 text-sm bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400">
                                    {{ __('or, enter the code manually') }}
                                </span>
                            </div>

                            <div
                                class="flex items-center space-x-2"
                                x-data="{
                                    copied: false,
                                    async copy() {
                                        try {
                                            await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                            this.copied = true;
                                            setTimeout(() => this.copied = false, 1500);
                                        } catch (e) {
                                            console.warn('Could not copy to clipboard');
                                        }
                                    }
                                }"
                            >
                                <div class="flex items-stretch w-full border rounded-xl dark:border-zinc-700">
                                    @empty($manualSetupKey)
                                        <div class="flex items-center justify-center w-full p-3 bg-zinc-100 dark:bg-zinc-700">
                                            <flux:icon.loading variant="mini"/>
                                        </div>
                                    @else
                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $manualSetupKey }}"
                                            class="w-full p-3 bg-transparent outline-none text-zinc-900 dark:text-zinc-100"
                                        />

                                        <button
                                            @click="copy()"
                                            class="px-3 transition-colors border-l cursor-pointer border-zinc-200 dark:border-zinc-700"
                                        >
                                            <flux:icon.document-duplicate x-show="!copied" variant="outline"></flux:icon>
                                            <flux:icon.check
                                                x-show="copied"
                                                variant="solid"
                                                class="text-green-500"
                                            ></flux:icon>
                                        </button>
                                    @endempty
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </flux:modal>
        @endif
    </x-settings.layout>
</section>
