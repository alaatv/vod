<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagGroupResource;
use App\Models\TagGroup;
use App\Models\TagGroup;

class TagGroupController extends Controller
{
    public function index()
    {
        $tagGroups = TagGroup::with(['tags'])
            ->enable()
            ->get();
        return TagGroupResource::collection($tagGroups);
    }
}
