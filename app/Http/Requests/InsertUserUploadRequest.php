<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InsertUserUploadRequest extends FormRequest
{
    public function authorize()
    {
        if (Auth::user()->completion() == 100) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'consultingAudioQuestions' => 'required|file|mimes:mp3,mpga|max:20480',
        ];
    }
}
