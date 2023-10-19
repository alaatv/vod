<?php

namespace App\Rules;

use App\Classes\VerificationCode;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class validateGuestCode implements Rule, DataAwareRule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return VerificationCode::checkCode(VerificationCode::RESEND_GUST, Arr::get($this->data, 'mobile'), $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('passwords.invalidCode');
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
