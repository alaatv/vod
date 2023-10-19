<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MapDetailRequest extends FormRequest
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
        return [
            'p1_lat' => ['required_with:p1_lng,p2_lat,p2_lng,zoom', 'numeric'],
            'p1_lng' => ['required_with:p1_lat,p2_lat,p2_lng,zoom', 'numeric'],
            'p2_lat' => ['required_with:p1_lat,p1_lng,p2_lng,zoom', 'numeric'],
            'p2_lng' => ['required_with:p1_lat,p1_lng,p2_lat,zoom', 'numeric'],
            'zoom' => ['required_with:p1_lat,p1_lng,p2_lat,p2_lng', 'numeric'],
        ];
    }
}
