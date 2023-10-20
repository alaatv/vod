<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class NotUpgradeProduct implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return DB::table('product_product')
                ->where([
                    'p2_id' => $value,
                    'relationtype_id' => config('constants.PRODUCT_INTERRELATION_UPGRADE')
                ])->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'محصول مورد نظر امکان اضافه شدن به سبد خرید را ندارد';
    }
}
