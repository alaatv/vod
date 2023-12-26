<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertCouponRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Notice: The enable, discount, usageNumber and usageLimit fields can't be null in database. But we used
            //  the "sometimes" rule for them because those have a default value in database.
            //  So for them, a valid value is considered in the database if no value is entered for them at all.
            'name' => 'required|string|min:2',
            'code' => 'nullable|string|min:1|alpha_dash|unique:coupons,code',
            'discount' => 'sometimes|required|numeric|between:0,100',
            // Notice: The usageNumber field shouldn't be specified for store mode.
            //            'usageNumber' => '',
            'usageLimit' => 'nullable|integer|min:0',
            'discounttype_id' => 'integer|min:1|exists:discounttypes,id',
            'enable' => 'sometimes|required|boolean',
            'is_strict' => 'sometimes|required|boolean',
            'description' => 'nullable|string|min:2',
            // The date sent in the request for validSince and validUntil fields is in 2021-05-08T07:03:00.000Z format.
            'validSince' => 'nullable|date',
            'validUntil' => 'nullable|date',
            'products' => 'nullable|array',
            'products.*' => 'sometimes|required|integer|min:1|distinct|exists:products,id',
            'required_products' => 'nullable|array',
            'required_products.*' => 'nullable|integer|exists:products,id',
            'unrequired_products' => 'nullable|array',
            'unrequired_products.*' => 'nullable|integer|exists:products,id',
            'number_of_code_digits' => 'integer|min:1',
            'prefix' => 'string|min:1',
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

        if (! is_null($input['validSince'])) {
            $date = explode('T', $input['validSince']);
            $input['validSince'] = $date[0];
        }
        if (! is_null($input['validUntil'])) {
            $date = explode('T', $input['validUntil']);
            $input['validUntil'] = $date[0];
        }

        $this->replace($input);
    }
}
