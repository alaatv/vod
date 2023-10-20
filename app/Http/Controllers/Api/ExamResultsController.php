<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BonyadEhsan\Exam\ExamResultRequest;
use App\Http\Requests\BonyadEhsan\Exam\StoreRequest;
use App\Http\Resources\BonyadEhsan\Exam\AverageLineResource;
use App\Http\Resources\BonyadEhsan\Exam\RankChartResource;
use App\Http\Resources\BonyadEhsan\Exam\RegressionLineResource;
use App\Http\Resources\BonyadEhsan\Exam\UserRankResource;
use App\Http\Resources\UserForBonyadEhsan;
use App\Jobs\BonyadEhsanExcelExportJob;
use App\Models\BonyadEhsanExcelExport;
use App\Models\User;
use App\Repositories\ExamRepository;
use App\Services\BonyadService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ExamResultsController extends Controller
{
    public function store(StoreRequest $request)
    {
        $roles = [
            config('constants.ROLE_BONYAD_EHSAN_MANAGER'),
            config('constants.ROLE_BONYAD_EHSAN_NETWORK'),
            config('constants.ROLE_BONYAD_EHSAN_SUB_NETWORK'),
            config('constants.ROLE_BONYAD_EHSAN_MOSHAVER'),
            config('constants.ROLE_BONYAD_EHSAN_USER'),
        ];
        $user = User::where('id', $request->get('user_id'))->whereHas('roles', function (Builder $query) use ($roles) {
            $query->whereIn('name', $roles);
        })->first();
        if ($user) {
            return $user->_3aExamResult()->updateOrCreate([
                    'exam_id' => $request->get('exam_id'), 'user_id' => $request->get('user_id')
                ]
                , [
                    'exam_lesson_data' => $request->get('exam_lesson_data'),
                    'exam_ranking_data' => $request->get('exam_ranking_data')
                ]);
        }
        return false;
    }

    public function rankChart(ExamResultRequest $request)
    {
        /** @var User $user */
        if ($request->has('user_id')) {
            $user = User::find($request->get('user_id'));
        } else {
            $user = auth('api')->user();
        }

        if ($user->hasRole(config('constants.ROLE_BONYAD_EHSAN_USER'))) {
            $data = ExamRepository::getRankChart($user->id, $user->major_id);
        } else {
            if (!$request->has('major')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => (object) [
                        'major' => [
                            'وارد کردن major الزامیست.'
                        ]
                    ]
                ], 422);
            }
            $data = ExamRepository::getRankChart($user->id, $request->get('major'), true);
        }


        return RankChartResource::collection([
            ['title' => 'رگرسیون', 'data' => RegressionLineResource::collection($data)],
            ['title' => 'تراز', 'data' => AverageLineResource::collection($data)]
        ]);
    }

    public function userRank(ExamResultRequest $request)
    {
        /** @var User $user */
        if ($request->has('user_id')) {
            $user = User::find($request->get('user_id'));
        } else {
            $user = auth('api')->user();
        }

        if ($user->hasRole(config('constants.ROLE_BONYAD_EHSAN_USER'))) {
            $datas = ExamRepository::getUserRank($user->id, $user->major_id);
        } else {
            if (!$request->has('major')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => (object) [
                        'major' => [
                            'وارد کردن major الزامیست.'
                        ]
                    ]
                ], 422);
            }
            $datas = ExamRepository::getUserRank($user->id, $request->get('major'), true);
        }

        $result = [];
        $resultAverageRank = [];
        foreach ($datas as $data) {
            $lessons = json_decode($data->exam_lesson_data, true);
            $resultAverageRank[$data->title][] = $data->exam_ranking_data;
            $result[$data->title]['title'] = $data->title;
            foreach ($lessons as $key => $lesson) {
                $result[$data->title]['lessons'][$key][] = $lesson['rank'];
            }
        }
        foreach ($resultAverageRank as $key => $average) {
            $resultAverageRank[$key] = (int) (array_sum($average) / count($average));
        }
        foreach ($result as $key1 => $res) {
            foreach ($res['lessons'] as $key2 => $val) {
                $result[$key1]['lessons'][$key2] = (int) (array_sum($val) / count($val)) - $resultAverageRank[$key1];
            }
        }
        $completeResult = [];
        foreach ($result as $row) {
            $lessons = [];
            foreach ($row['lessons'] as $key => $les) {
                $lessons[] = [
                    'title' => $key,
                    'value' => $les
                ];
            }
            $completeResult[] = [
                'title' => $row['title'],
                'lessons' => $lessons,
            ];
        }
        return UserRankResource::collection($completeResult);
    }

    public function averageRanking(ExamResultRequest $request)
    {
        /** @var User $user */
        if ($request->has('user_id')) {
            $user = User::find($request->get('user_id'));
        } else {
            $user = auth('api')->user();
        }

        if ($user->hasRole(config('constants.ROLE_BONYAD_EHSAN_USER'))) {
            $data = ExamRepository::getAverageRank($user->id, $user->major_id);
        } else {
            if (!$request->has('major')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => (object) [
                        'major' => [
                            'وارد کردن major الزامیست.'
                        ]
                    ]
                ], 422);
            }
            $data = ExamRepository::getAverageRank($user->id, $request->get('major'), true)[0];
        }
        return response()->json($data);
    }

    public function getUsers(ExamResultRequest $request)
    {
        $search = [];
        if ($request->has('first_name')) {
            $search['filterFirstName'] = $request->get('first_name');
        }
        if ($request->has('last_name')) {
            $search['filterLastName'] = $request->get('last_name');
        }
        if ($request->has('mobile')) {
            $search['filterMobile'] = $request->get('mobile');
        }
        if ($request->has('national_code')) {
            $search['filterNationalCode'] = $request->get('national_code');
        }


        if ($request->get('excel_export')) {
            $export = BonyadEhsanExcelExport::create([
                'user_id' => $request->user()->id,
            ]);
            BonyadEhsanExcelExportJob::dispatch($request->all(), $export->id, auth('api')->user());
            return response()->json([
                'data' => [
                    'id' => $export->id,
                    'progress' => 0,
                    'link' => null
                ]
            ]);
        }

        if ($request->has('action')) {
            $bonyadUsers = BonyadService::users(auth('api')->user()->id, $request->get('action'), search: $search);
        } else {
            /** @var User $user */
            if ($request->has('user_id')) {
                $user = User::find($request->get('user_id'));
            } else {
                $user = auth('api')->user();
            }
            $bonyadUsers = BonyadService::userLevel($user, search: $search);
        }
        return UserForBonyadEhsan::collection($bonyadUsers);
    }

    public function checkExport(BonyadEhsanExcelExport $excelExport)
    {
        if ($excelExport->user_id != auth('api')->user()->id) {
            return response()->json([
                'data' => ['message' => 'دسترسی مجاز نیست.']
            ], 403);
        }
        if ($excelExport->status == true and $excelExport->export_link != null) {
            $excelExport->delete();
            return response()->json([
                'data' => [
                    'id' => $excelExport->id,
                    'progress' => 100,
                    'link' => $excelExport->export_link
                ]
            ]);
        }
        if (!is_null($excelExport->status) and $excelExport->status == false) {
            return response()->json([
                'data' => ['message' => 'درخواست با خطا مواجه شد']
            ], 500);
        }

        $now = Carbon::now();
        $start = Carbon::parse($excelExport->created_at);
        $end = Carbon::parse($excelExport->created_at)->addSeconds($excelExport->total_user * config('constants.BONYAD_EXCEL_EXPORT_PER_USER_TIME'));
        if ($now > $end) {
            return response()->json([
                'data' => [
                    'id' => $excelExport->id,
                    'progress' => 97,
                    'link' => null
                ]
            ]);
        }


        $period = $end->timestamp - $start->timestamp;
        $now = $now->timestamp - $start->timestamp;
        return response()->json([
            'data' => [
                'id' => $excelExport->id,
                'progress' => $now * 100 / $period,
                'link' => null
            ]
        ]);


    }
}
