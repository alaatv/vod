<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductsInBlock;
use App\Models\Product;
use Illuminate\Http\Request;

class BlockProductsController extends Controller
{
    public function index(Request $request)
    {
        $fields = $request->get('fields', ['*']);
        $products = Product::getProducts(0, 0, [], 'created_at', 'desc')->get($fields);

        return ProductsInBlock::collection($products);
    }
}
