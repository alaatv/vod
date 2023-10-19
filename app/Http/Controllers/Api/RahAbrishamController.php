<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Grade as GradeResource;
use App\Http\Resources\Major as MajorResource;
use App\Http\Resources\StudyEventMethodResource;
use App\Http\Resources\StudyEventReportResource;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Product;
use App\Models\StudyEventMethod;
use App\Models\StudyEventMethod;
use App\Models\StudyEventReport;
use App\Models\StudyEventReport;
use Illuminate\Http\JsonResponse;

class RahAbrishamController extends Controller
{
    public function selectPlanCreate(): JsonResponse
    {
        $abrisham2Products = [];
        $abrisham2ProductsDetails = collect(Product::ABRISHAM_2_DATA);
        $userActiveStudyEvent = auth()->user()->getActiveStudyEvents()->first();
        if (isset($userActiveStudyEvent)) {
            $abrisham2ProductsDetails = $abrisham2ProductsDetails->reject(function ($detail) use ($userActiveStudyEvent
            ) {
                return !in_array($userActiveStudyEvent->major_id, $detail['majorIds']);
            });
        }
        foreach ($abrisham2ProductsDetails as $productId => $detail) {
            $product = [];
            $product['id'] = $productId;
            $product['lesson_name'] = $detail['lesson_name'];
            $abrisham2Products[] = $product;
        }
        return response()->json(
            [
                'data' => [
                    'grades' => GradeResource::collection(Grade::whereIn('id', [4, 8])->get()),
                    'majors' => MajorResource::collection(Major::whereIn('id', [1, 2, 3])->get()),
                    'studyPlans' => StudyEventMethodResource::collection(StudyEventMethod::all()),
                    'products' => $abrisham2Products,
                ],
            ]
        );
    }

    public function indexSystemReport()
    {
        $systemReports = auth()->user()->studyEventReports()->read(StudyEventReport::UN_READ_REPORT)->latest()->get();
        return StudyEventReportResource::collection($systemReports);
    }
}
