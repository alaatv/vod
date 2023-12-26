<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexShahrRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'ostan_id' => 'integer|min:1|exists:ostan,id',
        ];
    }
}
