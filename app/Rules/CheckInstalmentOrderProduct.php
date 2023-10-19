<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckInstalmentOrderProduct implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

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
        $installment_product_qty = $value->orderproducts()->whereHas('product', function ($query) {
            $query->where('has_instalment_option', 1);
        })->count();
        return $value->orderproducts->count() == $installment_product_qty;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'محصول انتخاب شده قابلیت خرید اقساطی نداره';
    }

}
