<?php

namespace App\Http\Requests;

use App\Models\Paymentstatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserOrdersRequest extends FormRequest
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
        $paymentStatusIds = Paymentstatus::all()->pluck('id');

        return [
            'since' => 'nullable|date',
            'till' => 'nullable|date',
            'paymentStatuses' => ['nullable', 'array'],
            'paymentStatuses.*' => [Rule::in($paymentStatusIds)],
        ];
    }
}
