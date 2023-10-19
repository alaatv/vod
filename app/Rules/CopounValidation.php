<?php

namespace App\Rules;

use App\Models\Coupon;
use Illuminate\Contracts\Validation\Rule;

class CopounValidation implements Rule
{
    private int $validationCode = 0;

    public function passes($attribute, $coupon)
    {
        if (is_null($coupon)) {
            $this->validationCode = Coupon::COUPON_VALIDATION_STATUS_NOT_FOUND;
            return 0;
        }
        /**
         * @var Coupon $coupon
         */
        $this->validationCode = $coupon->validateCoupon();

        if ($this->validationCode === Coupon::COUPON_VALIDATION_STATUS_OK) {
            return true;
        }

        return false;

    }


    public function message()
    {
        return trans('validation.'.Coupon::COUPON_VALIDATION_INTERPRETER[$this->validationCode]);
    }
}
