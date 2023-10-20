<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateContentSetRequest
 * @package App\Http\Requests
 */
class UpdateContentSetRequest extends FormRequest
{

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
            'newFileFullName' => 'required_without:newContetnsetId',
            'newContetnsetId' => 'required_without:newFileFullName',
        ];
    }
}
