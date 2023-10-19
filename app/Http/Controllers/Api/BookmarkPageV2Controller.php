<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Bookmark;
use App\Http\Resources\ResourceCollection;
use App\Models\User;
use App\Traits\User\AssetTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class  BookmarkPageV2Controller extends Controller
{
    use AssetTrait;

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
        $totalFavored = collect();
        $totalFavored = $totalFavored->merge($user->getTotalActiveFavoredContents()->addTypeIndex()->take(20));
        $totalFavored = $totalFavored->merge($user->getActiveFavoredSets()->addTypeIndex()->take(20));
        $totalFavored = $totalFavored->merge($user->getActiveFavoredProducts()->addTypeIndex()->take(20));
        return Bookmark::collection($totalFavored);

    }
}
