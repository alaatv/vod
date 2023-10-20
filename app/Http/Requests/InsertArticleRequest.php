<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertArticleRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.INSERT_ARTICLE_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'title' => 'required|max:100',
            'order' => 'required|numeric',
            'keyword' => 'max:200',
            'brief' => 'required|max:200',
            'body' => 'required',
            'image' => 'image|mimes:jpeg,jpg,png',
            'articlecategory_id' => 'exists:articlecategories,id',
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
        if (isset($input['order'])) {
            $input['order'] = preg_replace('/\s+/', '', $input['order']);
            $input['order'] = $this->convertToEnglish($input['order']);
        }
        $this->replace($input);
    }
}
