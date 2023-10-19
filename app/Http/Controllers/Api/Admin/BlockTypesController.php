<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BlockTypes;
use App\Models\BlockType;

class BlockTypesController extends Controller
{
    public function index()
    {
        return BlockTypes::collection(BlockType::all());
    }
}
