<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InsertPermissionRequest extends FormRequest
{
    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.INSERT_PERMISSION_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $serviceIds = [];
        foreach (array_values(config('constants.SERVICE_IDS')) as $value) {
            $serviceIds[] = $value['KEY'];
        }
        return [
            'name' => 'required',
            'display_name' => 'required',
            'service_id' => [
                'required',
                Rule::in($serviceIds),
                Rule::unique('permissions')->where(function ($query) {
                    return $query->where('service_id', $this->service_id)
                        ->where('name', $this->name);
                })
            ],
        ];
    }
}
