<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendBulkSmsRequest extends FormRequest
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
            'message' => 'required|string',
            'user_id' => 'required|array',
            'user_id.*' => 'required|exists:users,id',
        ];
    }

    protected function prepareForValidation()
    {
        if (!is_array($this->get('user_id')) && $this->get('user_id')) {
            $userIds = explode(',', $this->get('user_id'));
            $userIds = array_map(fn($userId) => trim($userId), $userIds);

            $this->merge([
                'user_id' => $userIds
            ]);
        }

    }
}
