<?php

namespace App\Http\Requests;

use App\Rules\CheckInstalmentOrderProduct;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Kreait\Firebase\Exception\RemoteConfig\ValidationFailed;

class RedirectToPaymentRequest extends FormRequest
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
            'order' => ['nullable', new CheckInstalmentOrderProduct()]
        ];
    }

    public function prepareForValidation()
    {
        if ($this->inInstalment) {
            $this->merge([
                'order' => auth()->user()?->getOpenOrderOrCreate($this->get('inInstalment', 0))
            ]);
        }

    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidationFailed($validator->errors()->first());
    }
}
