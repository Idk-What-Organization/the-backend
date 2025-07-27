<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ResendVerificationCooldownRule implements ValidationRule
{
    protected int $cooldownSeconds = 60;
    protected int $remainingSeconds = 0;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::where('email', $value)->first();

        if ($user && $user->last_verification_email_sent_at) {
            $secondsSinceLastSent = abs(now()->diffInSeconds($user->last_verification_email_sent_at));

            if ($secondsSinceLastSent < $this->cooldownSeconds) {
                $this->remainingSeconds = $this->cooldownSeconds - $secondsSinceLastSent;
                $fail($this->message());
            }
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return "Harap tunggu {$this->remainingSeconds} detik sebelum mencoba lagi.";
    }
}
