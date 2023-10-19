<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditContactRequest extends FormRequest
{
    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.EDIT_CONTACT_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        $contactId = $this->route('contact')->id;

        return [
            'name' => 'required',
            'contacttype_id' => 'exists:contacttypes,id',
            'relative_id' => 'unique:contacts,relative_id'.$contactId.'id,deleted_at,NULL|exists:relatives,id',

            'phoneNumber.*' => 'required|numeric',
            'priority.*' => 'numeric',
            'contact_id.*' => 'exists:contacts,id',
            'phonetype_id.*' => 'exists:phonetypes,id',
        ];
    }
}
