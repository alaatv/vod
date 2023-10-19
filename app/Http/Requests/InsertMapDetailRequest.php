<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class InsertMapDetailRequest extends FormRequest
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
            'map_id' => ['required', 'exists:maps,id'],
            'type_id' => ['required', 'exists:mapDetailTypes,id'],
            'entity_id' => ['required_with:entity_type'],
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
        $typeId = Arr::get($input, 'entity_id');
        //Because front end will send null as string and cant do anything about it and entity_id must be int
        if (is_string($typeId)) {
            $input['entity_id'] = null;
            $input['entity_type'] = null;
        }

        $this->replace($input);
    }
}
