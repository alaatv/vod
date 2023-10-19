<?php

namespace App\Http\Requests\BonyadEhsan\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationIndexRequest extends FormRequest
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
            'read' => ['required', Rule::in(['all', 'read', 'unread'])],
            'owner_id' => ['required'],
        ];
    }
}
