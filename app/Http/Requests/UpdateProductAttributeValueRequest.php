<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateProductAttributeValueRequest
 */
class UpdateProductAttributeValueRequest extends FormRequest
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
            'attributevalues' => 'required|array',
            'attributevalues.*' => 'integer|min:1|exists:attributevalues,id,deleted_at,NULL',
            'extraCost' => 'nullable|array',
            'description' => 'nullable|array',
        ];
    }
}
