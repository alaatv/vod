<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AttachProductAttributeValueRequest
 * @package App\Http\Requests
 */
class AttachProductAttributeValueRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'attribute_id' => 'required|integer|min:1|exists:attributes,id,deleted_at,NULL',
            'attribute_value_id' => 'required|integer|min:1|exists:attributevalues,id,deleted_at,NULL',
            'order' => 'integer|min:0',
            'extra_cost' => 'nullable|integer|min:0',
            'description' => 'nullable|string|min:2',
        ];
    }
}
