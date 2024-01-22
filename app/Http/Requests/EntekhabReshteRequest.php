<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class EntekhabReshteRequest extends FormRequest
{
    use CharacterCommon;

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
        return [
            'file' => ['mimes:jpeg,jpg,png,pdf'],
            'comment' => 'nullable|string',
            'consultant_firstname' => ['required_with:consultant_lastname,consultant_mobile', 'string'],
            'consultant_lastname' => ['required_with:consultant_firstname,consultant_mobile', 'string'],
            'consultant_mobile' => [
                'required_with:consultant_firstname,consultant_lastname',
                'digits:11',
                'phone:AUTO,IR',
            ],
            'shahrha' => ['required', 'string'],
            'majors' => ['array'],
            'majors.*' => ['nullable', 'string'],
            'university_types' => ['required', 'array'],
            'university_types.*' => ['integer', 'exists:university_types,id'],
            'phone' => 'required|numeric',
        ];
    }

    public function prepareForValidation()
    {
        $this->replaceNumbers();
        parent::prepareForValidation();
    }

    protected function replaceNumbers()
    {
        $input = $this->request->all();

        foreach ($input as $key => $value) {
            if ($value == 'null' || is_null($value)) {
                unset($input[$key]);
            }
        }

        if (isset($input['consultant_mobile'])) {
            $input['consultant_mobile'] = preg_replace('/\s+/', '', $input['consultant_mobile']);
            $input['consultant_mobile'] = $this->convertToEnglish($input['consultant_mobile']);
        }

        if (isset($input['phone'])) {
            $input['phone'] = preg_replace('/\s+/', '', $input['phone']);
            $input['phone'] = $this->convertToEnglish($input['phone']);
        }

        $this->replace($input);
    }
}
