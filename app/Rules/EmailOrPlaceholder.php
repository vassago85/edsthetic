<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailOrPlaceholder implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && (filter_var($value, FILTER_VALIDATE_EMAIL) || preg_match('/^\[[^\]]+\]$/', $value) === 1)) {
            return;
        }

        $fail('Enter an email address.');
    }
}
