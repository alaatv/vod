<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexBlockRequest extends FormRequest
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
     * @param  Request  $request
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'title' => 'nullable|string|min:2',
            'tags' => 'nullable|string|min:2',
//                'without_tags' => 'nullable|accepted',
            'customUrl' => 'nullable|url',
//                'without_customUrl' => 'nullable|accepted',
            'class_field' => 'nullable|string|min:2',
//                'without_class' => 'nullable|accepted',
            'active_status' => 'nullable|boolean',
            'types' => 'nullable|array',
            'types.*' => 'integer|min:1|exists:block_types,id,deleted_at,NULL',
        ];
    }
}
