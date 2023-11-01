<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;

class MarketingController extends Controller
{
    public function marketingAdmin()
    {
        $pageName = 'admin';
        $products = $this->makeProductCollection();
        return response()->json(compact('pageName', 'products'));

    }
}