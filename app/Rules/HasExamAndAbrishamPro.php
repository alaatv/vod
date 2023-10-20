<?php

namespace App\Rules;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class HasExamAndAbrishamPro implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private Product $product, private $user)
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
        if ($this->product->is3aExam() && $this->user->userHasAnyOfTheseProducts(array_keys(Product::ALL_ABRISHAM_PRO_PRODUCTS))) {
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
        return 'این آزمون هدیه شماست از "آزمون های من" وارد شو.';
    }
}
