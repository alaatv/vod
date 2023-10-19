<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlockInAsset;
use App\Http\Resources\ResourceCollection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardPageV2Controller extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     *
     * @param  User  $user
     *
     * @return JsonResponse|ResourceCollection
     */
    public function __invoke(Request $request, User $user = null)
    {
        /** @var User $user */
        if (!is_null($user) && $request->user()->id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'you can\'nt get user '.$user->id.' dashboard!');
        }

        $user = $request->user();

        //child has worker task
        return BlockInAsset::collection($user->getDashboardBlocksForApp());
    }
}
