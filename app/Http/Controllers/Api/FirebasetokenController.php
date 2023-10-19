<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsertFireBaseTokenRequest;
use App\Http\Resources\Firebasetoken as FirebasetokenResource;
use App\Models\Firebasetoken;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Validator;

class FirebasetokenController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertFireBaseTokenRequest  $request
     *
     * @return Firebasetoken|Application|ResponseFactory|JsonResponse|Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'token' => 'required',
        ])->validate();

        $user = $request->user();
        $token = Firebasetoken::where('token', $request->get('token'))->where('user_id', $user->id)->first();

        $refreshToken = optional($token)->refresh_token;
        if (!isset($refreshToken)) {
            $refreshToken = Str::random(36);
        }

        if (isset($token)) {
            if (!isset($token->refresh_token)) {
                try {
                    $token->update([
                        'refresh_token' => $refreshToken
                    ]);
                } catch (QueryException $e) {
                    return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $e->getMessage());
                }
            }

            return (new FirebasetokenResource($token))->response();
        }

        try {
            Firebasetoken::create([
                'user_id' => $user->id,
                'token' => $request->get('token'),
                'refresh_token' => $refreshToken,
            ]);
        } catch (QueryException $e) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $e->getMessage());
        }

        return (new FirebasetokenResource($token))->response();
    }

    public function storeByUser(InsertFireBaseTokenRequest $request, User $user)
    {
        $token = $request->get('token');
        $tokens = Firebasetoken::where('token', $token)->where('user_id', $user->id)->get();
        if ($tokens->isNotEmpty()) {
            $responseContent = [
                'message' => 'Token saved successfully',
            ];
            return response($responseContent);
        }


        $fireBaseToken = new Firebasetoken();
        $fireBaseToken->fill($request->all());
        $fireBaseToken->user_id = $user->id;
        $result = $fireBaseToken->save();

        if ($result) {
            $responseContent = [
                'message' => 'Token saved successfully',
            ];
        } else {
            $responseContent = [
                'error' => [
                    'code' => Response::HTTP_SERVICE_UNAVAILABLE,
                    'message' => 'Database error',
                ],
            ];
        }

        return response($responseContent, Response::HTTP_OK);
    }

    public function updateByRefreshToken(InsertFireBaseTokenRequest $request, string $refreshToken)
    {
        $user = $request->user();
        $token = Firebasetoken::where('refresh_token', $refreshToken)->where('user_id', $user->id)->first();
        if (!isset($token)) {
            return response(Response::HTTP_NOT_FOUND);
        }

        try {
            $token->update([
                'token' => $request->get('token'),
            ]);
        } catch (QueryException $e) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $e->getMessage());
        }

        return response();
    }

    public function destroyByRefreshToken(Request $request, string $refreshToken)
    {
        $user = $request->user();
        $token = Firebasetoken::where('refresh_token', $refreshToken)->where('user_id', $user->id)->first();
        if (!isset($token)) {
            return response(Response::HTTP_NOT_FOUND);
        }

        try {
            $token->forceDelete();
        } catch (QueryException $e) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $e->getMessage());
        }

        return response();
    }
}
