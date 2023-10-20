<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'commentable_id' => 'required|integer|min:1',
            'commentable_type' => 'required|string|min:1|in:'.implode(',',
                    array_keys(config('constants.MORPH_MAP_MODELS'))),
            'comment' => 'required|string|min:1',
        ];
    }
}
