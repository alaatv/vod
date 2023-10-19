<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertContactRequest extends FormRequest
{
    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.INSERT_CONTACT_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        if ($this->request->get('relative_id') == 0) {
            $this->request->set('relative_id', null);
        }
        $userId = $this->get('user_id');

        return [
            'name' => 'required',
            'contacttype_id' => 'exists:contacttypes,id',
            'relative_id' => 'unique:contacts,relative_id,NULL,id,deleted_at,NULL,user_id,'.$userId,
            'user_id' => 'exists:users,id',
        ];
    }
}
