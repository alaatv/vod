<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\AttributeValueSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditAttributevalueRequest;
use App\Http\Requests\InsertAttributevalueRequest;
use App\Http\Resources\Admin\AttributeValueResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Attributevalue;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AttributeValueController extends Controller
{
    /**
     * Return a listing of the resource.
     *
     * @param  AttributeValueSearch  $attributeValueSearch
     * @return ResourceCollection
     */
    public function index(AttributeValueSearch $attributeValueSearch)
    {
        // Set the number of items on each page.
        if (request()->has('length') && request()->length > 0) {
            $attributeValueSearch->setNumberOfItemInEachPage(request()->get('length'));
        }

        // Filter resources based on received parameters.
        $attributeValueResult = $attributeValueSearch->get(request()->all());

        return AttributeValueResource::collection($attributeValueResult);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertAttributevalueRequest|Attributevalue  $request
     * @return JsonResponse
     */
    public function store(InsertAttributevalueRequest $request)
    {
        $attributeSet = new Attributevalue();

        $this->fillAttributeSetFromRequest($request->all(), $attributeSet);

        try {
            $attributeSet->save();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new AttributeValueResource($attributeSet->refresh()))->response();
    }

    /**
     * Fill the model object to be stored or updated in database.
     *
     * @param  array  $inputData
     * @param  Attributevalue  $attributeValue
     */
    private function fillAttributeSetFromRequest(array $inputData, Attributevalue $attributeValue): void
    {
        $attributeValue->fill($inputData);
    }

    /**
     * Return the specified resource.
     *
     * @param  Attributevalue  $attributeValue
     * @return AttributeValueResource
     */
    public function show(Attributevalue $attributeValue)
    {
        return (new AttributeValueResource($attributeValue));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditAttributevalueRequest|Attributevalue  $request
     * @param  Attributevalue  $attributevalue
     * @return JsonResponse
     */
    public function update(EditAttributevalueRequest $request, Attributevalue $attributevalue)
    {
        $this->fillAttributeSetFromRequest($request->all(), $attributevalue);

        try {
            $attributevalue->update();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new AttributeValueResource($attributevalue->refresh()))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Attributevalue  $attributeValue
     */
    public function destroy(Attributevalue $attributeValue)
    {
        try {
            $attributeValue->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json();
    }
}
