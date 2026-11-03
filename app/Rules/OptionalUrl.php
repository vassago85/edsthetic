<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OptionalUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value) || (is_string($value) && filter_var($value, FILTER_VALIDATE_URL))) {
            return;
        }

        $fail('Enter a full URL, including https://.');
    }
}
