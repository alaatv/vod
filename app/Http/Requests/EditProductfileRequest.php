<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class EditProductfileRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => 'required_without_all:cloudFile',
            'cloudFile' => 'required_without_all:file',
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
        if (isset($input['cloudFile'])) {
            $input['cloudFile'] = preg_replace('/\s+/', '', $input['cloudFile']);
            $input['cloudFile'] = $this->convertToEnglish($input['cloudFile']);
        }

        if (isset($input['order'])) {
            $input['order'] = preg_replace('/\s+/', '', $input['order']);
            $input['order'] = $this->convertToEnglish($input['order']);
        }

        $this->replace($input);
    }
}
