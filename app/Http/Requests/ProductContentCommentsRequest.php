<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductContentCommentsRequest extends FormRequest
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
            'search' => 'string',
            'contentset_title' => 'string',
            'created_at_since' => 'string',
            'created_at_till' => 'string',
            'limit' => 'int',
        ];
    }
}
