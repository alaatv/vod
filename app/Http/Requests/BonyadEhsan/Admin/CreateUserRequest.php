<?php

namespace App\Http\Requests\BonyadEhsan\Admin;

use App\Models\Order;
use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

/**
 * Class EditOrderRequest
 * @package App\Http\Requests
 * @mixin Order
 */
class CreateUserRequest extends FormRequest
{
    use CharacterCommon;

    protected $id;

    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        return [
            'firstName' => ['required', 'max:255'],
            'lastName' => ['required', 'max:255'],
            'mobile' => ['required', 'digits:11', 'phone:AUTO,IR'],
            'nationalCode' => ['required', 'digits:10', 'validate:nationalCode'],
            'gender_id' => ['required', 'integer', 'min:1', 'exists:genders,id'],
            'major_id' => ['required', 'integer', 'min:1', 'exists:majors,id'],
            'shahr_id' => ['required', 'integer', 'min:1', 'exists:shahr,id'],
            'address' => ['required'],
            'phone' => ['required', 'digits_between:1,15'],
            'father_mobile' => ['required', 'digits:11', 'phone:AUTO,IR'],
            'mother_mobile' => ['required', 'digits:11', 'phone:AUTO,IR'],
        ];
    }
}
