<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditAttributegroupRequest;
use App\Http\Requests\InsertAttributegroupRequest;
use App\Http\Requests\Request;
use App\Models\Attribute;
use App\Models\Attributegroup;

class AttributegroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_ATTRIBUTEGROUP_ACCESS'), ['only' => 'index']);
        $this->middleware('permission:'.config('constants.INSERT_ATTRIBUTEGROUP_ACCESS'), ['only' => 'store']);
        $this->middleware('permission:'.config('constants.REMOVE_ATTRIBUTEGROUP_ACCESS'), ['only' => 'destroy']);
        $this->middleware('permission:'.config('constants.SHOW_ATTRIBUTEGROUP_ACCESS'), ['only' => 'edit']);
        $this->middleware('permission:'.config('constants.UPDATE_ATTRIBUTEGROUP_ACCESS'), ['only' => 'update']);
    }

    public function index(Request $request)
    {
        $attributesetId = $request->get('attributeset_id');
        $attributegroups = Attributegroup::where('attributeset_id', $attributesetId)->get();

        return response()->json(['attributegroups' => $attributegroups], 200);
    }

    public function store(InsertAttributegroupRequest $request)
    {
        $attributegroup = new Attributegroup();
        $attributegroup->fill($request->all());

        if ($attributegroup->save()) {
            $attributegroup->attributes()->sync($request->get('attributes', []));
            return response()->json([], 200);
        }

        return response()->json(['message' => 'Service Unavailable'], 503);
    }

    public function edit(Attributegroup $attributegroup)
    {
        $attributeset = $attributegroup->attributeset_id;
        $attributes = Attribute::pluck('displayName', 'id')->toArray();
        $groupAttributes = $attributegroup->attributes()->pluck('id')->toArray();

        return response()->json([
            'attributegroup' => $attributegroup,
            'attributeset' => $attributeset,
            'groupAttributes' => $groupAttributes,
            'attributes' => $attributes
        ], 200);
    }

    public function update(EditAttributegroupRequest $request, Attributegroup $attributegroup)
    {
        $attributeset = $attributegroup->attributeset_id;
        $attributegroup->fill($request->all());
        $attributegroup->attributes()->sync($request->get('attributes', []));

        if ($attributegroup->update()) {
            return response()->json(['message' => 'اطلاعات گروه صفت با موفقیت اصلاح شد'], 200);
        } else {
            return response()->json(['message' => 'خطای پایگاه داده'], 500);
        }
    }

    public function destroy(Attributegroup $attributegroup)
    {
        if ($attributegroup->delete()) {
            return response()->json(['message' => 'گروه صفت با موفقیت حذف شد'], 200);
        } else {
            return response()->json(['message' => 'خطای پایگاه داده'], 500);
        }
    }
}