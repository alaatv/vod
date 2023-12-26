<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingRequest extends FormRequest
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
        $rules = [
            'service_id' => [
                'nullable',
                Rule::unique('settings')->where(function ($query) {
                    return $query->where('service_id', $this->input('service_id'))->where('key', $this->input('key'));
                }),
            ],
            'key' => 'required',
            'value' => ['required', 'json'],
        ];
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            unset($rules['key']);
            $rules['service_id'] = [
                'nullable',
                Rule::unique('settings')->where(function ($query) {
                    return $query->where('service_id', $this->input('service_id'))->where('key',
                        $this->route('setting')->key);
                })->ignore($this->route('setting')),
            ];
        }

        return $rules;
    }
}
