<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\SlideShowInBlock;
use App\Models\Slideshow;

class BlockSlideshowController extends Controller
{
    public function index()
    {
        return SlideShowInBlock::collection(Slideshow::all());
    }
}
