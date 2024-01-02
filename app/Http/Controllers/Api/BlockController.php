<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetBlockReguest;
use App\Http\Resources\Block as BlockResource;
use App\Http\Resources\BlockWithBlockableResource;
use App\Models\Block;
use App\Traits\FileCommon;
use App\Traits\RequestCommon;

class BlockController extends Controller
{
    use FileCommon;
    use RequestCommon;

    public function index()
    {
        return BlockResource::collection(Block::getMainBlocksForAppV2())->response();
    }

    public function show(Block $block)
    {
        return (new BlockResource($block))->response();
    }

    public function block(GetBlockReguest $request)
    {
        $data = Block::where('type', $request->get('type'))
            ->where('enable', '=', true);
        if ($request->has('blockable_type')) {
            $data = $data->whereRelation('blockables', function ($query) use ($request) {
                return $query->where('blockable_type',
                    'App\\' . ucfirst($request->get('blockable_type')))->where('deleted_at', null);
            })
                ->with([
                    'blockables' => function ($query) use ($request) {
                        return $query->where('blockable_type',
                            'App\\Models\\' . ucfirst($request->get('blockable_type')))->where('deleted_at',
                            null)->with('blockable');
                    },
                ]);
        } else {
            $data = $data->with('blockables.blockable');
        }

        $data = $data->orderBy('order')->get();

        return BlockWithBlockableResource::collection($data);
    }
}
