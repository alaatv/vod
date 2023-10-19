<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueNationalCodeWithPhoneRule implements Rule
{

    private $userId;
    private $nationalCode;
    private $phone;

    /**
     * Create a new rule instance.
     *
     * @param $userId
     * @param $nationalCode
     * @param $phone
     */
    public function __construct($userId, $nationalCode, $phone)
    {

        $this->userId = $userId;
        $this->nationalCode = $nationalCode;
        $this->phone = $phone;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !((bool) User::where('nationalCode', $this->nationalCode)
            ->where('phone', $this->phone)
            ->where('id', '<>', $this->userId)
            ->count());

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'کاربری یا این کدملی و شماره تلفن در سایت ثبت نام کرده است';
    }
}
