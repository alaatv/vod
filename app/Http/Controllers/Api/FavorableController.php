<?php

namespace App\Http\Controllers\Api;

use App\Classes\FavorableInterface;
use App\Events\FavoredTimePoint;
use App\Events\UnFavoredContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\MarkFavorableFavoriteRequest;
use App\Models\Content;
use App\Models\FavorableList;
use App\Models\Timepoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FavorableController extends Controller
{
    public function __construct()
    {
        $this->callMiddlewares($this->getAuthExceptionArray());
    }

    private function callMiddlewares($authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
    }

    private function getAuthExceptionArray(): array
    {
        return ['getUsersThatFavoredThisFavorable'];
    }

    public function markFavorableFavorite(
        MarkFavorableFavoriteRequest $request,
        FavorableInterface $favorable
    ): JsonResponse {
        $user = $request->user();
        if ($request->has('favorable_list_id')) {
            $favorableList = FavorableList::find($request->input('favorable_list_id'));
            if (! Gate::allows('show-update-delete-favorable-list', $favorableList)) {
                return myAbort(Response::HTTP_FORBIDDEN, 'شما فقط مجاز به اضافه کردن به لیست علاقه مندی خود هستید');
            }
        }
        $favorable->favoring($user, $request->validated());
        if ($favorable instanceof Timepoint) {
            FavoredTimePoint::dispatch($user, $favorable->content);
        }
        Cache::tags(['user_'.$user->id.'_favorites'])->flush();
        if ($favorable instanceof Timepoint) {
            Cache::tags(['content_'.optional(optional($favorable)->content)->id.'_timepoints'])->flush();
        }

        return response()->json([
            'message' => 'Favorite added successfully',
        ]);
    }

    public function markUnFavorableFavorite(Request $request, FavorableInterface $favorable): JsonResponse
    {
        $user = $request->user();
        $favorable->unfavoring($user);
        if ($favorable instanceof Content) {
            UnFavoredContent::dispatch($user, $favorable->times);
        }
        Cache::tags(['user_'.$user->id.'_favorites'])->flush();
        if ($favorable instanceof Timepoint) {
            Cache::tags(['content_'.optional(optional($favorable)->content)->id.'_timepoints'])->flush();
        }

        return response()->json([
            'message' => 'Favorite removed successfully',
        ]);
    }

    public function getUsersThatFavoredThisFavorable(Request $request, FavorableInterface $favorable)
    {
        $key = md5($request->url());

        return Cache::tags(['favorite', 'favorite_'.$favorable->id])->remember($key, config('constants.CACHE_1'),
            function () use ($favorable) {
                return $favorable->favoriteBy()
                    ->get()
                    ->count();
            });
    }
}
