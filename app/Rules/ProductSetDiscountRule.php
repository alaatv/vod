<?php

namespace App\Rules;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class ProductSetDiscountRule implements Rule
{
    public const TYPES = [
        'محصولات نمیتوانند شامل آیدی های 434 و 224 و 629 و 684 باشند',
        'محصولات نمیتوانند دارای دسته بندی ها با نام آزمون سه آ و تلسکوپ و کمک مالی باشند',
        'محصولات باید فعال باشند',
    ];
    private int $type;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $products = Product::whereIn('id', $value)->get();
        if ($products->contains(function ($product) {
            return in_array($product->id, [
                Product::COUPON_PRODUCT, Product::ASIATECH_PRODUCT, Product::YALDA_SUBSCRIPTION, Product::SHOROO_AZ_NO
            ]);
        })) {
            $this->type = 0;
            return false;
        }
        if ($products->contains(function ($product) {
            return in_array($product->category, ['Donation', 'تلسکوپ', 'آزمون/سه آ']);
        })) {
            $this->type = 1;
            return false;
        }
        if ($products->contains('enable', 0)) {
            $this->type = 2;
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return match ($this->type) {
            0 => self::TYPES[0],
            1 => self::TYPES[1],
            2 => self::TYPES[2],
        };
    }
}
