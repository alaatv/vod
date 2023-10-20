<?php

namespace App\Rules;

use App\Traits\CharacterCommon;
use Illuminate\Contracts\Validation\Rule;

class NotEmptyString implements Rule
{
    use CharacterCommon;

    /**
     * @var string
     */
    private $message;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->strIsEmpty($value)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans($this->message);
    }
}
