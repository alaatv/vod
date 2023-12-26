<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\In;

class EditSlideShowRequest extends FormRequest
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

        $os = isset($input['hasValidSince']) && $input['hasValidSince'];     // os = old since
        $ns = isset($input['hasValidSinceNew']) && $input['hasValidSinceNew'];  // ns = new since
        $ou = isset($input['hasValidUntil']) && $input['hasValidUntil'];     // ou = old until
        $nu = isset($input['hasValidUntilNew']) && $input['hasValidUntilNew'];  // nu = new until

        if ($ns && isset($input['validSinceDate'])) {
            $input['validSince'] = $input['validSinceDate'].' '.($input['validSinceTime'] ?? '00:00:00');
        }
        if (! $os && ! $ns) {
            $input['validSince'] = null;
        }

        if ($nu && isset($input['validUntilDate'])) {
            $input['validUntil'] = $input['validUntilDate'].' '.($input['validUntilTime'] ?? '00:00:00');
        }
        if (! $ou && ! $nu) {
            $input['validUntil'] = null;
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
            'title' => ['required', 'string', 'max:250'],
            'shortDescription' => ['nullable', 'string', 'max:199'],
            'photo' => ['nullable', 'image', 'max:1500', 'mimes:jpg,png'],
            'in_new_tab' => ['nullable', new In([0, 1])],
            'order' => ['nullable', 'integer', 'min:0'],
            'link' => ['nullable', 'url', 'max:250'],
            'isEnable' => ['nullable', new In([0, 1])],
            'validSinceDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'validUntilDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            // TODO: Check why the this rule not work:  'after:validSinceDate'
            'validSinceTime' => ['nullable', 'date_format:H:i:s'],
            'validUntilTime' => ['nullable', 'date_format:H:i:s'],
            'validSince' => ['nullable', 'date', 'date_format:Y-m-d H:i:s'],
            'validUntil' => ['nullable', 'date', 'date_format:Y-m-d H:i:s'],
            'width' => ['nullable', 'numeric'],
            'height' => ['nullable', 'numeric'],
        ];
    }
}
