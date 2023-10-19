<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentStatusResource;
use App\Models\ContentsStatus;
use App\Models\ContentsStatus;

class ContentStatusController extends Controller
{
    public function index()
    {
        return ContentStatusResource::collection(ContentsStatus::all());
    }
}
