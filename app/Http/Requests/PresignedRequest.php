<?php

namespace App\Http\Requests;

use App\Rules\PreSignedUrlRule;
use Illuminate\Foundation\Http\FormRequest;

class PresignedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]+\.[A-Za-z]{3}/u', new PreSignedUrlRule()],
        ];
    }

    public function messages()
    {
        return [
            'key.regex' => 'از کاراکترهای فارسی و فاصله استفاده نکنید',
        ];
    }
}
