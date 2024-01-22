<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Rules\Own;
use Illuminate\Foundation\Http\FormRequest;

class EditTicketMessageRequest extends FormRequest
{
    private $user;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        $this->user = $request->user();

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->user->isAbleTo(config('constants.INSERT_TICKET_ACCESS'))) {
            $ticketRule = 'exists:App\Ticket,id';
        } else {
            $ticketRule = new Own($this->user->id, Ticket::class);
        }

        return [
            'ticket_id' => [$ticketRule],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg', 'max:512'],
            'voice' => ['nullable', 'mimetypes:audio/ogg'],

        ];
    }
}
