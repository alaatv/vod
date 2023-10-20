<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Rules\NotEmptyString;
use App\Rules\Own;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class InsertTicketMessageRequest extends FormRequest
{
    private $user;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param  Request  $request
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        $input = $this->input();

        $this->user = $request->user();
//        if(!optional($this->user)->isAbleTo(config('constants.INSERT_TICKET_ACCESS'))){
        $input['user_id'] = $this->user->id;
//        }

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
        if ($this->user->isAbleTo(config('constants.INSERT_TICKET_ACCESS'))) {
            $ticketRule = 'exists:App\Ticket,id';
        } else {
            $ticketRule = new Own($this->user->id, Ticket::class, 'user_id');
        }
        return [
            'ticket_id' => ['required', $ticketRule],
            'photo' => ['required_without_all:body,voice,file', 'image', 'mimes:jpeg,jpg', 'max:5120'],
            'voice' => ['required_without_all:photo,body,file', 'mimetypes:audio/ogg,video/webm'],
            'file' => ['required_without_all:photo,body,voice', 'mimes:pdf,zip,rar'],
            'body' => ['required_without_all:photo,voice,file', 'string', new NotEmptyString()],
        ];
    }

    public function prepareForValidation()
    {
        $input = $this->request->all();

        $input['body'] = strip_tags(Arr::get($input, 'body'), ['br']);

        $this->replace($input);

        parent::prepareForValidation();
    }
}
