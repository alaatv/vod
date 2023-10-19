<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveNewBlockRequest;
use App\Http\Requests\SyncBlockProductsRequest;
use App\Http\Requests\UpdateBlockRequest;
use App\Http\Resources\Block as BlockResource;
use App\Http\Resources\BlockInAdmin;
use App\Models\Block;
use App\Traits\FileCommon;
use App\Traits\ProductCommon;
use App\Traits\RequestCommon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
    use FileCommon;
    use RequestCommon;
    use ProductCommon;

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_BLOCK_ACCESS'))->only(['index', 'show']);
        $this->middleware('permission:'.config('constants.EDIT_BLOCK_ACCESS'))->only(['update']);
        $this->middleware('permission:'.config('constants.INSERT_BLOCK_ACCESS'))->only([
            'store', 'syncProducts', 'syncSets', 'syncBanners', 'syncContents'
        ]);
    }

    public function index()
    {
        return BlockResource::collection(Block::getMainBlocksForAppV2());
    }

    public function show(Block $block)
    {
        return new BlockResource($block);
    }

    public function store(SaveNewBlockRequest $request)
    {
        $block = Block::create($request->validated());
        return new BlockResource($block);
    }

    public function update(UpdateBlockRequest $request, Block $block)
    {
        $block->update($request->validated());
        return new BlockInAdmin($block);
    }

    public function syncProducts(SyncBlockProductsRequest $request, Block $block)
    {
        $block->attachProducts($request->validated()['block_products'], shouldLog: true);

        $block->updateBlockableOrder($request->validated()['block_products_order'], ['products']);

        return new BlockResource($block->fresh());
    }

    public function syncSets(Block $block, Request $request)
    {
        $setsId = $request->get('block-sets', []);
        $block->attachSets($setsId, shouldLog: true);
        $block->updateBlockableOrder(Arr::get($request->get('blockable_orders'), 'sets'), ['sets']);
        return new BlockResource($block->refresh());
    }

    public function syncBanners(Block $block, Request $request)
    {
        $slideId = $request->get('block-slides', []);
        $block->attachBanners($slideId, shouldLog: true);
        $block->updateBlockableOrder(Arr::get($request->get('blockable_orders'), 'banners'), ['banners']);
        return new BlockResource($block->fresh());
    }

    public function syncContents(Block $block, Request $request)
    {
        $contentsId = $request->get('block-contents');
        $block->attachContents($contentsId, shouldLog: true);
        $block->updateBlockableOrder(Arr::get($request->get('blockable_orders'), 'contents'), ['contents']);
        return new BlockResource($block->fresh());
    }

    public function destroy(Block $block)
    {
        $block->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
