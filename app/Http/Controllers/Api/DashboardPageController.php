<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DashboardPageController extends Controller
{
    /**
     * DashboardPageController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function __invoke(Request $request, User $user)
    {
        if ($request->user()->id !== $user->id) {
            abort(ResponseAlias::HTTP_FORBIDDEN, 'you can\'nt get user '.$user->id.' dashboard!.');
        }
        //child has worker task
        $userAssetsCollection = $user->getDashboardBlocksForApp();

        return response()->json([
            'user_id' => $user->id,
            'data' => $userAssetsCollection,
        ]);
    }
}
