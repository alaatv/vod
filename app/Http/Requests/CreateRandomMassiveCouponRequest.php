<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRandomMassiveCouponRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $this->prepareValidDateTime();
        parent::prepareForValidation();
    }

    protected function prepareValidDateTime()
    {
        $input = $this->request->all();

        if (!is_null($input['validSinceDate'])) {
            $input['validSince'] = $input['validSinceDate'].' '.($input['validSinceTime'] ?? '00:00:00');
        }

        if (!is_null($input['validUntilDate'])) {
            $input['validUntil'] = $input['validUntilDate'].' '.($input['validUntilTime'] ?? '00:00:00');
        }

        $this->replace($input);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => 'required|integer|min:1|max:100000',
            'discount' => 'required|integer|between:0,100',
            'name' => 'nullable|string|min:1|max:255',
            'codePrefix' => 'nullable|string|min:1|max:251',
            'usageLimit' => 'nullable|integer|min:1|max:100000',
            'description' => 'nullable|string|min:1',
            'validSinceDate' => ['nullable', 'date_format:Y-m-d'],
            'validUntilDate' => ['nullable', 'date_format:Y-m-d'],
            'validSinceTime' => ['nullable', 'date_format:H:i:s'],
            'validUntilTime' => ['nullable', 'date_format:H:i:s'],
            'validSince' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'validUntil' => ['nullable', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
