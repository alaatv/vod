<?php

namespace App\Rules;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class ProductBelongsSeller implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private Product $product, private $seller)
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
        if ($this->seller != $this->product->seller) {
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
        return 'محصول متعلق به این فروشگاه نیست.';
    }
}
