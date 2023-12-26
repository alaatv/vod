<?php

namespace App\Http\Requests;

use App\Models\Coupontype;
use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EditCouponRequest
 */
class EditCouponRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|min:2',
            'code' => 'sometimes|required|string|min:1|alpha_dash|unique:coupons,code,'.$this->coupon->id,
            'discount' => 'sometimes|required|numeric|between:0,100',
            'usageNumber' => 'sometimes|required|integer|min:0',
            'usageLimit' => 'nullable|integer|min:0',
            'coupontype_id' => 'sometimes|required|integer|min:1|exists:coupontypes,id',
            'discounttype_id' => 'sometimes|required|integer|min:1|exists:discounttypes,id',
            'enable' => 'sometimes|required|boolean',
            'is_strict' => 'sometimes|required|boolean',
            'description' => 'nullable|string|min:2',
            'validSince' => 'nullable|date',
            'validUntil' => 'nullable|date',
            'products' => 'sometimes|required_if:coupontype_id,'.Coupontype::ATTRIBUTE_TYPE_OVERALL_ID.'|array',
            'products.*' => 'sometimes|required|integer|min:1|distinct|exists:products,id',
            'required_products' => 'nullable|array',
            'required_products.*' => 'nullable|integer|exists:products,id',
            'unrequired_products' => 'nullable|array',
            'unrequired_products.*' => 'nullable|integer|exists:products,id',
        ];
    }

    public function prepareForValidation()
    {
        $this->replaceNumbers();
        parent::prepareForValidation();
    }

    protected function replaceNumbers()
    {
        $input = $this->request->all();

        $items = ['code', 'discount', 'usageNumber', 'usageLimit'];
        foreach ($items as $item) {
            if (isset($input[$item])) {
                $input[$item] = $this->convertToEnglish($input[$item]);
            }
        }

        $this->replace($input);
    }
}
