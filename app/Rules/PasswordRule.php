<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = $value;

        if (strlen($password) < 8) {
            $fail(__('validation.password.min', ['attribute' => $attribute, 'min' => 8]));

            return;
        }

        if (! preg_match('/[A-Z]/', $password)) {
            $fail(__('The :attribute must contain at least one uppercase letter.'));
        }

        if (! preg_match('/[a-z]/', $password)) {
            $fail(__('The :attribute must contain at least one lowercase letter.'));
        }

        if (! preg_match('/[0-9]/', $password)) {
            $fail(__('The :attribute must contain at least one number.'));
        }
    }
}
