<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateProductAttributeValueRequest
 */
class GroupRegistrationRequest extends FormRequest
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
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10000'],
            'userStatusId' => ['sometimes', 'accepted'],
            'productIds' => ['nullable', 'array'],
            'productIds.*' => ['nullable', 'integer', 'min:1', 'exists:products,id,deleted_at,NULL'],
            'giftIds' => ['nullable', 'array'],
            'giftIds.*' => ['nullable', 'integer', 'min:1', 'exists:products,id,deleted_at,NULL'],
            'discount' => ['nullable', 'integer', 'min:0', 'max:100'],
            'paymentStatusId' => ['nullable', 'integer', 'min:1', 'exists:paymentstatuses,id,deleted_at,NULL'],
            'orderStatusId' => ['nullable', 'integer', 'min:1', 'exists:orderstatuses,id,deleted_at,NULL'],
        ];
    }
}
