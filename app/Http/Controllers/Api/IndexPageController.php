<?php

namespace App\Http\Controllers\Api;

use App\Classes\Format\BlockCollectionFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlockV2;
use App\Models\Block;
use Illuminate\Http\Request;

/**
 * Class IndexPageController
 * Only application requests are sent to this class.
 *
 * @package App\Http\Controllers\Api
 */
class IndexPageController extends Controller
{
    public function __invoke(Request $request, BlockCollectionFormatter $blockCollectionFormatter)
    {
        return BlockV2::collection(Block::getMainBlocksForAppV2());
    }
}
