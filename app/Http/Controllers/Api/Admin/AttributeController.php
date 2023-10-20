<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\AttributeSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditAttributeRequest;
use App\Http\Requests\InsertAttributeRequest;
use App\Http\Requests\Request;
use App\Http\Resources\Admin\AttributeResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Attribute;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

/**
 * Class AttributeController.
 * For Api Version 2.
 * For Admin side.
 *
 * @package App\Http\Controllers\Api\Admin
 */
class AttributeController extends Controller
{
    /**
     * Return a listing of the resource.
     *
     * @param  AttributeSearch  $attributeSearch
     * @return ResourceCollection
     */
    public function index(AttributeSearch $attributeSearch)
    {
        // Set the number of items on each page.
        if (request()->has('length') && request()->get('length') > 0) {
            $attributeSearch->setNumberOfItemInEachPage(request()->get('length'));
        }

        // Filter resources based on received parameters.
        $attributeResult = $attributeSearch->get(request()->all());

        return AttributeResource::collection($attributeResult);
    }

    /**
     * Return the specified resource.
     *
     * @param  Attribute  $attribute
     * @return JsonResponse|AttributeResource|RedirectResponse|Redirector
     */
    public function show(Attribute $attribute)
    {
        return (new AttributeResource($attribute));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertAttributeRequest|Attribute  $request
     * @return JsonResponse
     */
    public function store(InsertAttributeRequest $request)
    {
        $attribute = new Attribute();

        $this->fillAttributeFromRequest($request->all(), $attribute);

        try {
            $attribute->save();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', $e], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new AttributeResource($attribute->refresh()))->response();
    }

    /**
     * Fill the model object to be stored or updated in database.
     *
     * @param  array|Request  $inputData
     * @param  Attribute  $attribute
     */
    private function fillAttributeFromRequest(array $inputData, Attribute $attribute): void
    {
        $attribute->fill($inputData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditAttributeRequest|Attribute  $request
     * @param  Attribute  $attribute
     * @return JsonResponse
     */
    public function update(EditAttributeRequest $request, Attribute $attribute)
    {
        $this->fillAttributeFromRequest($request->all(), $attribute);

        try {
            $attribute->update();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new AttributeResource($attribute->refresh()))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Attribute  $attribute
     * @return Exception|JsonResponse
     * @throws Exception
     */
    public function destroy(Attribute $attribute)
    {
        try {
            $attribute->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json();
    }
}
