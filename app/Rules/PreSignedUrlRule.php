<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PreSignedUrlRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (
            str_ends_with($value, '.png') ||
            str_ends_with($value, '.jpg') ||
            str_ends_with($value, '.jpeg') ||
            str_ends_with($value, '.gif') ||
            str_ends_with($value, '.pdf') ||
            str_ends_with($value, '.json')
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'پسوند نام فایل معنبر نیست.';
    }
}
