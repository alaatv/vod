<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTicketMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        $input = $this->input();

        $user = $request->user();
        if (!optional($user)->isAbleTo(config('constants.SHOW_TICKET_ACCESS'))) {
            $input['user_id'] = $user->id;
        }

        $this->replace($input);

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
            //
        ];
    }
}
