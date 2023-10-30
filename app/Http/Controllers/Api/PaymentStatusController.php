<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Paymentstatus;
use App\PaymentModule\GtmEec;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentStatusController extends Controller
{
    public function index()
    {
        return Paymentstatus::collection(Paymentstatus::all());
    }

    public function show(Request $request, string $status, string $paymentMethod, string $device): JsonResponse
    {
        $sessionToken = $request->session()->token();
        $result = $request->session()->pull('verifyResult');

        $user = $request->user();
        $needCompleteInfo = isset($user) && $user->completion() < 60;

        if ($result == null) {
            return response()->json(['message' => 'Verification result not found'], 404);
        }

        $gtmEec = (new GtmEec())->generateGtmEec($result['orderId'], $device, $result['paidPrice']);

        $responseData = [
            'status' => $status,
            'paymentMethod' => $paymentMethod,
            'device' => $device,
            'result' => $result,
            'gtmEec' => $gtmEec,
            'user' => $user,
            'needCompleteInfo' => $needCompleteInfo,
            'sessionToken' => $sessionToken
        ];

        return response()->json($responseData);
    }

}
