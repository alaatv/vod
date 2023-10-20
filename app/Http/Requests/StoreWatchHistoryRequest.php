<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWatchHistoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'watchable_id' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('watch_histories')
                    ->where('watchable_id', $this->watchable_id)
                    ->where('user_id', $this->user()->id)
            ],
            'watchable_type' => 'required|string|min:1|in:'.implode(',',
                    array_keys(config('constants.MORPH_MAP_MODELS'))),
            'seconds_watched' => 'int|min:1',
            'studyevent_id' => 'nullable|integer|exists:studyevents,id',
            'completely_watched' => ['sometimes', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'watchable_id.unique' => ':attribute قبلا مشاهده شده است.'
        ];
    }
}
