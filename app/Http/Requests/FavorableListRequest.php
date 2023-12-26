<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FavorableListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'title' => ['required', 'string'],
                'order' => ['required', 'integer'],
            ];
        }

        return [
            'title' => 'string',
            'order' => 'integer',
        ];
    }
}
