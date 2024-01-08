<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\TicketDepartment;
use App\Rules\Enable;
use App\Rules\NotEmptyString;
use App\Rules\Own;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class InsertTicketRequest extends FormRequest
{
    private $user;

    /**
     * Determine if the user is authorized to make this request.
     *
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        $input = $this->input();

        $this->user = $request->user();
        $requestUserId = Arr::get($input, 'user_id');
        if (! optional($this->user)->isAbleTo(config('constants.INSERT_TICKET_ACCESS')) || ! isset($requestUserId)) {
            $input['user_id'] = $this->user->id;
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
        $input = $this->input();
        $orderproductRule = '';
        if ($this->user->isAbleTo(config('constants.INSERT_TICKET_ACCESS'))) {
            $orderproductRule = 'exists:App\Models\Orderproduct,id';
            $orderRule = 'exists:App\Models\Order,id';
            $departmentRule = 'exists:App\Models\TicketDepartment,id';

            if ($this->request->has('orderproduct_id')) {
                $orderproduct = Orderproduct::find($this->request->get('orderproduct_id'));
                $input['order_id'] = optional($orderproduct)->order_id;
            }

        } else {
            if ($this->request->has('orderproduct_id')) {
                if ($this->request->has('order_id')) {
                    $orderproductRule = new Own($this->request->get('order_id'), Orderproduct::class, 'order_id');
                } else {
                    $orderproduct = Orderproduct::find($this->request->get('orderproduct_id'));
                    $input['order_id'] = optional($orderproduct)->order_id;
                }
            }

            $orderRule = new Own($this->user->id, Order::class, 'user_id');
            $departmentRule = new Enable(TicketDepartment::class);
        }

        $this->replace($input);

        return [
            'title' => ['required'],
            'department_id' => ['required', $departmentRule],
            'orderproduct_id' => [$orderproductRule],
            'order_id' => [$orderRule],
            'photo' => ['required_without_all:body,voice,file', 'image', 'mimes:jpeg,jpg', 'max:5120'],
            'voice' => ['required_without_all:photo,body,file', 'mimetypes:audio/ogg,video/webm'],
            'file' => ['required_without_all:photo,body,voice', 'mimes:zip,txt,pdf'],
            'body' => ['required_without_all:photo,voice,file', 'string', new NotEmptyString()],
            'priority_id' => ['numeric'],
        ];
    }

    public function prepareForValidation()
    {
        $input = $this->request->all();

        $input['title'] = strip_tags(Arr::get($input, 'title'));
        $input['body'] = strip_tags(Arr::get($input, 'body'), ['br']);

        $this->replace($input);

        parent::prepareForValidation();
    }
}
