<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TempOasisAttendant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TempOasisAttendantController extends Controller
{
    /**
     * @param  Request|TempOasisAttendant  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'mobile_prefix' => ['required', 'min:1'],
            'mobile_number' => ['required', 'min:1'],
        ]);

        $tempOasisAttendant = TempOasisAttendant::where('email', $request->email)
            ->where('mobile_prefix', $request->mobile_prefix)
            ->where('mobile_number', $request->mobile_number);
        if ($tempOasisAttendant->exists()) {
            $createdAt = $tempOasisAttendant->first(['created_at'])->created_at;
            return response()->json(['message' => "You have registered before at {$createdAt} (UTC)"],
                Response::HTTP_BAD_REQUEST);
        }

        try {
            TempOasisAttendant::create($request->all());
        } catch (Exception $exception) {
            return response()->json(['message' => 'Database error!', 'errorInfo' => $exception],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(['message' => 'Registration successfully.'], Response::HTTP_OK);
    }
}
