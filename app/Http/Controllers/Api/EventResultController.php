<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\EventResultSearch;
use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\InsertEventResultRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventResultResource;
use App\Http\Resources\EventResultStatusResource;
use App\Models\Event;
use App\Models\Eventresult;
use App\Models\Eventresultstatus;
use App\Models\Major;
use App\Models\Product;
use App\Models\Region;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EventResultController extends Controller
{
    public function index(Request $request, EventResultSearch $eventResultSearch)
    {
        $inputs = [
            'user_id' => auth()->id(),
            'event_id' => $request->query('event_id'),
        ];
        if (is_null($inputs['event_id'])) {
            unset($inputs['event_id']);
        }
        return EventResultResource::collection($eventResultSearch->get($inputs));
    }

    public function create(): JsonResponse
    {
        $eventsId = [1, 3, 4, 8, 9, 12, 13];
        return response()->json([
            'data' => [
                'events' => EventResource::collection(Event::whereIn('id', $eventsId)->get()),
                'eventResultStatuses' => EventResultStatusResource::collection(Eventresultstatus::all()),
                'regions' => Region::all(),
                'majors' => Major::all()->except([4]) // 4 = id of علوم و معارف اسلامی
            ]
        ]);
    }

    public function store(InsertEventResultRequest $request): JsonResponse
    {
        $user = $request->user();
        $inputs = $request->all();
        if (
            $request->has('participationCode') &&
            strlen(preg_replace('/\s+/', '', $request->input('participationCode'))) != 0
        ) {
            $inputs['participationCode'] = encrypt($request->get('participationCode'));
            $inputs['participationCodeHash'] = bcrypt($request->get('participationCode'));
        }
        if ($request->has('user_id')) {
            if ($user->isAbleTo(config('constants.INSET_EVENTRESULT_ACCESS'))) {
                $inputs['user_id'] = $request->input('user_id');
            } else {
                return response()->json([
                    'message' => 'شما دسترسی انجام را ندارید',
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            $inputs['user_id'] = $user->id;
        }

        $file = $request->file('reportFile');
        $inputs['reportFile'] = Uploader::put($file, config('disks.EVENT_RESULT_MINIO_TEMP'));
        $user->update($this->userDetails($request));
        Eventresult::updateOrCreateEventResult($inputs);
        if (!$user->orders()->paidAndClosed()->whereHas('orderproducts', function ($q) {
            $q->where('product_id', Product::MOSHAVERE_ENTEKHAB_RESHTE);
        })->exists()) {
            $order = OrderRepo::createBasicCompletedOrder($user->id, config('constants.PAYMENT_STATUS_PAID'), 0, 0);
            OrderproductRepo::createGiftOrderproduct($order->id, Product::MOSHAVERE_ENTEKHAB_RESHTE, 0);
        }
        return response()->json([
            'message' => 'event result created successfully',
        ], Response::HTTP_CREATED);
    }

    private function userDetails($request): array
    {
        $userDetails = [];
        if ($request->has('shahr_id')) {
            $userDetails['shahr_id'] = $request->input('shahr_id');
        }
        if ($request->has('postalCode')) {
            $userDetails['postalCode'] = $request->input('postalCode');
        }
        return $userDetails;
    }

    public function show(Eventresult $eventresult): EventResultResource
    {
        return new EventResultResource($eventresult);
    }

    public function getInfoByEvent(Request $request, Event $event)
    {
        $authUser = $request->user();
        if ($request->has('user_id')) {
            if ($authUser?->isAbleTo(config('constants.GET_EVENTRESULT_ACCESS'))) {
                $userId = $request->input('user_id');
            } else {
                return response()->json([
                    'message' => 'شما دسترسی انجام را ندارید',
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            $userId = $authUser->id;
        }
        $result = Eventresult::whereEventId($event->id)->whereUserId($userId)->first();
        return new EventResultResource($result);
    }
}
