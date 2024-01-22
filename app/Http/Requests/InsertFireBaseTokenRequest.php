<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertFireBaseTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        $authorized = true;
        $authenticatedUser = $request->user('api');
        $userId = $request->segment(4);
        if ($userId != $authenticatedUser->id) {
            $authorized = false;
        }

        return $authorized;
    }

    public function rules()
    {
        return [
            'token' => 'required',
        ];
    }
}
