<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertContentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     *
     * @return array
     */
    public function rules(Request $request)
    {
        if ($request->get('contenttype_id') == config('constants.CONTENT_TYPE_ARTICLE')) {
            $rules = [
                //                'order'          => 'numeric',
                'name' => 'required',
                'context' => 'required',
                'contenttype_id' => 'required|exists:contenttypes,id',
            ];
        } else {
            $rules = [
                'order' => 'required|numeric',
                'name' => 'required',
                'contenttype_id' => 'required|exists:contenttypes,id',
                'contentset_id' => 'required|exists:contentsets,id',
                'fileName' => 'required',
            ];
        }

        return $rules;
    }
}
