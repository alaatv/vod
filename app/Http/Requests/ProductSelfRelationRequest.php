<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductSelfRelationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'relation' => [
                'required',
                Rule::in([
                    config('constants.PRODUCT_INTERRELATION_GIFT'),
                    config('constants.PRODUCT_INTERRELATION_UPGRADE'),
                    config('constants.PRODUCT_INTERRELATION_ITEM'),
                ])
            ],
            'related_product_ids' => ['required', 'array'],
            'related_product_ids.*' => ['required', Rule::exists(Product::getTableName(), 'id')],
            'choiceable' => 'boolean',
            'required_when' => [
                'nullable', $this->input('required_when') != 'null' ? Rule::exists(Product::getTableName(), 'id') : ''
            ],
        ];
    }
}
