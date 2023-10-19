<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentInComeCalculateRequest;
use App\Http\Requests\Admin\ContentInComeRequest;
use App\Models\ContentIncome;
use App\Models\ContentIncome;
use Carbon\Carbon;

class ContentInComeController extends Controller
{


    /**
     * ContentInComeController constructor.
     */
    public function __construct()
    {
        $this->middleware('role:'.config('constants.ROLE_ADMIN').'|'.config('constants.ROLE_AUDITOR'));
    }

    public function index(ContentInComeRequest $request)
    {
        $user = $request->user();
        $contentInCome = ContentInCome::unZeroCost();

        if ($request->has('content_id')) {
            $contentInCome->where('content_id', $request->get('content_id'));
        }

        if ($request->has('date')) {
            $date = Carbon::parse($request->get('date'));
            $contentInCome->whereDay('transaction_completed_at', $date->day)
                ->whereMonth('transaction_completed_at', $date->month)
                ->whereYear('transaction_completed_at', $date->year);
        }

        if ($user->hasRole(config('constants.ROLE_AUDITOR'))) {
            $contentInCome->accountant();
        }

        return ContentInComeRequest::collection($contentInCome->get());
    }

    public function groupIndex(ContentInComeCalculateRequest $request)
    {
        $user = $request->user();
        $date = Carbon::parse($request->get('date'));
        $contentInCome = ContentInCome::unZeroCost()
            ->whereDay('transaction_completed_at', $date->day)
            ->whereMonth('transaction_completed_at', $date->month)
            ->whereYear('transaction_completed_at', $date->year);

        if ($user->hasRole(config('constants.ROLE_AUDITOR'))) {
            $contentInCome->accountant();
        }

        $result = $contentInCome->get()->groupBy('content_id')->map(function ($contentInCome, $content_id) {
            $sum = $contentInCome->sum(ContentIncome::getAuthorizedShareCostIndex());
            return [
                'content_id' => $content_id,
                'sum' => bcdiv($sum, 1, 2),
                'count' => $contentInCome->count(),
            ];
        })->values();

        return ContentInComeRequest::collection($result);
    }

    public function show()
    {

    }

}
