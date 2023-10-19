<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\VoipSocket;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoipWebsocketRequest;
use App\Http\Resources\User as UserResources;
use App\Repositories\UserRepo;
use App\Repositories\VoipOperatorRepo;
use App\Repositories\VoipRepo;
use Symfony\Component\HttpFoundation\Response;


class VoipController extends Controller
{
    public function sendUserToAdmin(VoipWebsocketRequest $request)
    {
        $operatorID = VoipOperatorRepo::getOperatorIdFromLocalPhone($request->operator_local_phone);
        $user = UserRepo::find($request->caller_phone, $request->user_national_code)->first();
        $voipID = VoipRepo::storeٰVoipContact($request, $user?->id, $operatorID);
        $user = $user ? new UserResources($user) : 'unknown';
        event(new VoipSocket($user, $operatorID));
        return response()->json([
            'message' => 'store data successfully !!!! ',
            'voipDocumentID' => $voipID
        ], Response::HTTP_OK);
    }
}
