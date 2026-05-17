<?php

namespace App\Livewire;

use App\Notifications\EmailVerificationCode;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VerifyEmailCode extends Component
{
    public string $code = '';

    public bool $codeSent = false;

    public function mount(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            redirect()->intended(config('fortify.home', '/dashboard'));
        }

        // Auto-send code on page load if not already sent
        $this->sendCode();
    }

    public function sendCode(): void
    {
        $user = Auth::user();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'email_verification_code' => $code,
            'email_verification_expires_at' => now()->addMinutes(10),
        ])->save();

        $user->notify(new EmailVerificationCode($code));
        $this->codeSent = true;

        session()->flash('status', 'A new verification code has been sent to your email.');
    }

    public function verify(): void
    {
        $user = Auth::user();

        if (
            $user->email_verification_code !== $this->code ||
            now()->isAfter($user->email_verification_expires_at)
        ) {
            $this->addError('code', 'Invalid or expired code. Please request a new one.');

            return;
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ])->save();

        redirect()->intended(config('fortify.home', '/dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}
