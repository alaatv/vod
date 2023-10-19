<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SetsInBlock;
use App\Models\Contentset;
use App\Models\Contentset;


class BlockSetsController extends Controller
{
    public function index()
    {
        return SetsInBlock::collection(Contentset::orderByDesc('created_at')->get());
    }
}
