<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertProductRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //            'name'            => 'required',
            //            'basePrice'       => 'required|numeric',
            //            'discount'        => 'sometimes|numeric',
            //            'order'           => 'sometimes|numeric',
            //            'amount'          => 'required_if:amountLimit,1',
            //            'image'           => 'sometimes|image|mimes:jpeg,jpg,png',
            //            'file'            => 'sometimes|file',
            //            'attributeset_id' => 'required|exists:attributesets,id',
            'bonPlus' => 'sometimes|numeric|min:0',
            'bonDiscount' => 'sometimes|numeric|min:0|max:100',
            //            'producttype_id'  => 'required|exists:producttypes,id',

            'category' => 'nullable|string|min:2|max:191',
            'grand_id' => 'nullable|integer|min:1|exists:products,id',
            // TODO: I check it and I think the following item isn't used anywhere. So it's not needed. Please you check it too.
            //            'redirectUrl' => 'nullable|string|min:2|max:191',
            'name' => 'required|string|min:2|max:255',
            'shortName' => 'required|string|min:2|max:100',
            'basePrice' => 'required|integer|min:0',
            'discount' => 'sometimes|integer|min:0',
            'isFree' => 'sometimes|boolean',
            'amount' => 'nullable|integer|min:1',
            'shortDescription' => 'nullable|string|min:2',
            'longDescription' => 'nullable|string|min:2',
            'specialDescription' => 'nullable|string|min:2',
            'tags' => 'nullable|string|min:2',
            'recommender_contents' => 'nullable|string|min:2',
            'sample_contents' => 'nullable|string|min:2',
            'slogan' => 'nullable|string|min:2',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png',
            'file' => 'sometimes|file',
            'intro_videos' => 'nullable',
            'validSince' => 'nullable|date_format:Y-m-d H:i:s',
            'validUntil' => 'nullable|date_format:Y-m-d H:i:s',
            'enable' => 'sometimes|boolean',
            'display' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
            'page_view' => 'nullable|string|min:2|max:191',
            'producttype_id' => 'required|integer|min:1|exists:producttypes,id',
            'attributeset_id' => 'required|integer|min:1|exists:attributesets,id',
            'redirectCode' => 'required_with:redirectUrl',
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

        $items = ['order', 'discount', 'amount'];
        foreach ($items as $item) {
            if (isset($input[$item])) {
                $input[$item] = $this->convertToEnglish($input[$item]);
            }
        }

        $this->replace($input);
    }
}
