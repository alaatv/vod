<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkFavorableFavoriteRequest extends FormRequest
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
            'favorable_list_id' => ['integer', 'exists:favorable_lists,id'],
        ];
    }
}
