<?php

namespace App\Http\Requests;

use App\Repositories\AttributeRepo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionInquiryRequest extends FormRequest
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
        $attributes = AttributeRepo::getAttributesByType(config('constants.ATTRIBUTE_TYPE_SUBSCRIPTION'))->pluck('name');
        return [
            'access' => ['required', Rule::in($attributes)],
            'increment' => ['nullable', 'numeric'],
        ];
    }
}
