<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudyPlanRequest extends FormRequest
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
            'event_id' => ['nullable', 'integer', 'min:1', 'exists:studyevents,id,deleted_at,NULL'],
            'row' => ['integer', 'min:1'],
            'voice' => ['string', 'min:1', 'max:191'],
            'body' => ['string', 'min:1', 'max:191'],
            'title' => ['string', 'min:1', 'max:191'],
            'date' => ['string', 'min:1', 'max:191'],
            'plan_date' => ['date_format:Y-m-d'],
        ];
    }
}
