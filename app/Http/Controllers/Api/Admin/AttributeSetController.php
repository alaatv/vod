<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\AttributeSetSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditAttributesetRequest;
use App\Http\Requests\InsertAttributesetRequest;
use App\Http\Resources\Admin\AttributeSetResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Attributeset;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

/**
 * Class AttributeSetController.
 * For Api Version 2.
 * For Admin side.
 *
 * @package App\Http\Controllers\Api\Admin
 */
class AttributeSetController extends Controller
{
    /**
     * Return a listing of the resource.
     *
     * @param  AttributeSetSearch  $attributeSetSearch
     * @return ResourceCollection
     */
    public function index(AttributeSetSearch $attributeSetSearch)
    {
        // Set the number of items on each page.
        if (request()->has('length') && request()->length > 0) {
            $attributeSetSearch->setNumberOfItemInEachPage(request()->get('length'));
        }

        // Filter resources based on received parameters.
        $attributeSetResult = $attributeSetSearch->get(request()->all());

        return AttributeSetResource::collection($attributeSetResult);
    }

    /**
     * Return the specified resource.
     *
     * @param  Attributeset  $attributeSet
     * @return JsonResponse|AttributeSetResource|RedirectResponse|Redirector
     */
    public function show(Attributeset $attributeSet)
    {
        return (new AttributeSetResource($attributeSet));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertAttributesetRequest|Attributeset  $request
     * @return JsonResponse
     */
    public function store(InsertAttributesetRequest $request)
    {
        $attributeSet = new Attributeset();

        $this->fillAttributeSetFromRequest($request->all(), $attributeSet);

        try {
            $attributeSet->save();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new AttributeSetResource($attributeSet->refresh()))->response();
    }

    /**
     * Fill the model object to be stored or updated in database.
     *
     * @param  array  $inputData
     * @param  Attributeset  $attributeSet
     */
    private function fillAttributeSetFromRequest(array $inputData, Attributeset $attributeSet): void
    {
        $attributeSet->fill($inputData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditAttributesetRequest|Attributeset  $request
     * @param  Attributeset  $attributeSet
     * @return JsonResponse
     */
    public function update(EditAttributesetRequest $request, Attributeset $attributeSet)
    {
        $this->fillAttributeSetFromRequest($request->all(), $attributeSet);

        try {
            $attributeSet->update($request->all());
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new AttributeSetResource($attributeSet->refresh()))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Attributeset  $attributeSet
     * @return Exception|JsonResponse
     * @throws Exception
     */
    public function destroy(Attributeset $attributeSet)
    {
        try {
            $attributeSet->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json();
    }
}
