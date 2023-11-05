<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditAttributevalueRequest;
use App\Http\Requests\InsertAttributevalueRequest;
use App\Models\Attribute;
use App\Models\Attributevalue;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AttributevalueController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_ATTRIBUTEVALUE_ACCESS'),
            ['only' => 'index', 'productAttributeValueIndex', 'attributeAttributeValueIndex']);
        $this->middleware('permission:'.config('constants.INSERT_ATTRIBUTEVALUE_ACCESS'), ['only' => 'create']);
        $this->middleware('permission:'.config('constants.REMOVE_ATTRIBUTEVALUE_ACCESS'), ['only' => 'destroy']);
        $this->middleware('permission:'.config('constants.SHOW_ATTRIBUTEVALUE_ACCESS'), ['only' => 'edit']);
        $this->middleware('permission:'.config('constants.UPDATE_ATTRIBUTEVALUE_ACCESS'), ['only' => 'update']);
    }

    public function store(InsertAttributevalueRequest $request)
    {
        $attributevalue = new Attributevalue();
        $attributevalue->fill($request->all());

        if ($attributevalue->save()) {
            return response()->json();
        }
        return response()->json([], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }

    public function edit(Attributevalue $attributevalue)
    {
        $attribute = Attribute::findOrFail($attributevalue->attribute_id);

        return response()->json(compact('attribute', 'attributevalue'), ResponseAlias::HTTP_OK);
    }

    public function update(EditAttributevalueRequest $request, Attributevalue $attributevalue)
    {
        $attribute = Attribute::findOrFail($attributevalue->attribute_id);
        $attributevalue->fill($request->validated());

        if ($attributevalue->update()) {
            return response()->json(['success' => 'اطلاعات مقدار صفت با موفقیت اصلاح شد'],
                ResponseAlias::HTTP_OK);
        }

        return response()->json(['error' => 'خطای پایگاه داده'], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }

    public function destroy(Attributevalue $attributevalue)
    {
        if ($attributevalue->delete()) {
            return response()->json(['success' => 'مقدار صفت با موفقیت حذف شد'], ResponseAlias::HTTP_OK);
        }

        return response()->json(['error' => 'خطای پایگاه داده'], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }

    public function attributeAttributeValueIndex(Attribute $attribute)
    {
        return response()->json($attribute->attributevalues, ResponseAlias::HTTP_OK);
    }

    public function productAttributeValueIndex(Product $product)
    {
        return response()->json($product->attributevalues->pluck('id'), ResponseAlias::HTTP_OK);
    }
}