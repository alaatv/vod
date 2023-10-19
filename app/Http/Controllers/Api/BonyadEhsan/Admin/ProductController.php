<?php

namespace App\Http\Controllers\Api\BonyadEhsan\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SelectOptionResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.BONYAD_PRODUCT_ACCESS_SELECT_OPTION'),
            ['only' => 'selectOption']);
    }

    public function selectOption()
    {
        $oldAbrisham = Product::where('category', 'LIKE', 'VIP')->get(['id', 'name']);
        return SelectOptionResource::collection($oldAbrisham);
    }
}
