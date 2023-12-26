<?php

namespace App\Http\Requests\OrderProduct;

use App\Models\Product;
use App\Rules\HasExamAndAbrishamPro;
use App\Rules\NotUpgradeProduct;
use App\Rules\ProductBelongsSeller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderProductStoreRequest extends FormRequest
{
    private $product;

    public function authorize()
    {

        return true;
    }

    public function rules()
    {
        $productIdRules = [
            'bail', 'required',
            new NotUpgradeProduct(),
            'exclude_if:has_instalment_option,false',
            Rule::exists('products', 'id')->where('has_instalment_option', 1),
        ];
        if (isset($this->product)) {
            $productIdRules += [
                new ProductBelongsSeller($this->product, $this->input('seller', config('constants.ALAA_SELLER'))),
                new HasExamAndAbrishamPro($this->product, $this->user()),
            ];
        }

        return [
            'order_id' => 'required|int|min:0',
            'has_instalment_option' => 'boolean',
            'product_id' => $productIdRules,
            'seller' => 'nullable|integer',
            'products' => 'sometimes|required|array',
            'products.*' => 'sometimes|required|numeric',
            'attribute' => 'sometimes|required|array',
            'attribute.*' => 'sometimes|required|numeric',
            'extraAttribute' => 'sometimes|required|array',
            'extraAttribute.*' => 'sometimes|required|array',
            'extraAttribute.*.id' => 'sometimes|required|numeric',
            'withoutBon' => 'sometimes|required|boolean',
        ];
    }

    public function prepareForValidation()
    {
        $array = ['has_instalment_option' => $this->has_instalment_option ? 1 : 0];
        if ($this->has('product_id')) {
            $this->product = Product::find($this->product_id);
            $array += ['product' => $this->product];
        }

        return $this->merge($array);
    }
}
