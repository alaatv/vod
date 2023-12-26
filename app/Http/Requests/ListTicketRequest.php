<?php

namespace App\Http\Requests;

use App\Classes\Search\TicketDepartmentSearch;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ListTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     *
     *
     * @return bool
     */
    public function authorize(Request $request, TicketDepartmentSearch $departmentSearch)
    {
        $user = $request->user();

        if ($user->hasRole(config('constants.ROLE_ADMIN'))) {
            return true;
        }

        $input = $this->input();

        if (! optional($user)->isAbleTo(config('constants.INDEX_TICKET_ACCESS'))) {

            $input['user_id'] = $user->id;
            $this->replace($input);

            return true;
        }

        $ticketDepartments = $departmentSearch->get(['tags' => [$user->id]])->pluck('id')->toArray();
        if (empty($ticketDepartments)) {
            return false;
        }

        if (Arr::has($input, 'department_id')) {
            $ticketDepartments = array_intersect($ticketDepartments, Arr::get($input, 'department_id'));
        }

        $input['department_id'] = $ticketDepartments;
        $this->replace($input);

        return true;
    }

    public function prepareForValidation()
    {
        $input = $this->request->all();

        if (Arr::has($input, 'created_at_since')) {
            $input['created_at_since'] = Carbon::parse(Arr::get($input, 'created_at_since'),
                'Asia/Tehran')->timezone('UTC')->toDateTimeString();
        }

        if (Arr::has($input, 'created_at_till')) {
            $input['created_at_till'] = Carbon::parse(Arr::get($input, 'created_at_till'),
                'Asia/Tehran')->timezone('UTC')->toDateTimeString();
        }

        $this->replace($input);

        parent::prepareForValidation();
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
