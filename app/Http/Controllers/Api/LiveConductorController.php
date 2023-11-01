<?php

namespace App\Http\Controllers\Api;

use App\Events\GetLiveConductor;
use App\Exports\DefaultClassExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\LiveConductorResource;
use App\Models\Conductor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class LiveConductorController extends Controller
{
    public function index(Request $request)
    {
        $liveConductors = Conductor::where('date', '>', '2023-05-22')
            ->orderBy('date', 'desc')
            ->get();
        $participantWithProductIds = [];
        $buyersWithProductIds = [];
        $notParticipatedBuyersWithProductIds = [];
        if ($request->has('live_conductor_ids')) {
            $liveConductorIds = $request->query('live_conductor_ids');
            $filteredLiveConductorsProductIds = Conductor::whereIn('id', $liveConductorIds)
                ->pluck('product_id')
                ->toArray();
            $participants = User::with([
                'liveConductors' => function ($query) use ($liveConductorIds) {
                    $query->whereIn('live_conductor_id', $liveConductorIds);
                }
            ])->whereHas('liveConductors', function ($query) use ($liveConductorIds) {
                $query->whereIn('live_conductor_id', $liveConductorIds);
            })->get();
            foreach ($participants as $participant) {
                $participatedProductIds = [];
                foreach ($participant->liveConductors as $liveConductor) {
                    $participatedProductIds[] = $liveConductor->product_id;
                }
                $participantWithProductIds[$participant->id] = array_unique($participatedProductIds);
            }
            $liveConductorProductBuyers = User::with(
                [
                    'orders' => function ($query) use ($filteredLiveConductorsProductIds) {
                        $query->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                            ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'))
                            ->with([
                                'orderproducts' => function ($query) use ($filteredLiveConductorsProductIds) {
                                    $query->whereIn('product_id', $filteredLiveConductorsProductIds);
                                }
                            ])->whereHas(
                                'orderproducts',
                                function (Builder $query) use ($filteredLiveConductorsProductIds) {
                                    $query->whereIn('product_id', $filteredLiveConductorsProductIds);
                                }
                            );
                    }
                ]
            )->whereHas(
                'orders',
                function (Builder $query) use ($filteredLiveConductorsProductIds) {
                    $query->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                        ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'))
                        ->whereHas(
                            'orderproducts',
                            function (Builder $query) use ($filteredLiveConductorsProductIds) {
                                $query->whereIn('product_id', $filteredLiveConductorsProductIds);
                            }
                        );
                }
            )->get();
            foreach ($liveConductorProductBuyers as $liveConductorProductBuyer) {
                $purchasedProductIds = [];
                foreach ($liveConductorProductBuyer->orders as $order) {
                    foreach ($order->orderproducts as $orderproduct) {
                        $purchasedProductIds[] = $orderproduct->product_id;
                    }
                }
                $buyersWithProductIds[$liveConductorProductBuyer->id] = $purchasedProductIds;
            }
            foreach ($buyersWithProductIds as $userId => $productIds) {
                $notParticipatedProductIds = [];
                foreach ($productIds as $productId) {
                    if (!array_key_exists($userId, $participantWithProductIds) || !in_array($productId,
                            $participantWithProductIds[$userId])) {
                        $notParticipatedProductIds[] = $productId;
                    }
                }
                if (!empty($notParticipatedProductIds)) {
                    $notParticipatedBuyersWithProductIds[$userId] = $notParticipatedProductIds;
                }
            }
        }
        return response()->json([
            'liveConductors' => $liveConductors,
            'participantWithProductIds' => $participantWithProductIds,
            'notParticipatedBuyersWithProductIds' => $notParticipatedBuyersWithProductIds

        ]);
    }

    public function view(Request $request)
    {
        $liveConductor = Conductor::where('class_name', $request->input('class_name'))
            ->whereDate('date', Carbon::today()->setTimezone('Asia/Tehran'))
            ->where('start_time', '<', now()->setTimezone('Asia/Tehran'))
            ->where('finish_time', '>', now()->setTimezone('Asia/Tehran'))
            ->first();
        if (!isset($liveConductor)) {
            return myAbort(Response::HTTP_FAILED_DEPENDENCY, 'زنگی یافت نشد');
        }
        GetLiveConductor::dispatch($liveConductor, auth()->id());
        return response()->json([
            'message' => "live conductor's user has been updated successfully",
        ]);
    }

    public function show(Conductor $liveConductor)
    {
        if (!($liveConductor->date == Carbon::today()->toDateString()
            && $liveConductor->start_time < now()->setTimezone('Asia/Tehran')
            && $liveConductor->finish_time > now()->setTimezone('Asia/Tehran'))) {
            return myAbort(Response::HTTP_FAILED_DEPENDENCY, 'لایو هنوز شرووع نشده است');
        }
        GetLiveConductor::dispatch($liveConductor, auth()->id());
        return new LiveConductorResource($liveConductor);
    }

    public function report(Request $request)
    {
        $usersWithProducts = $request->input('participants') ?? $request->input('notParticipants');
        $disk = config('disks.LIVE_CONDUCTOR_REPORT');
        $excelHeaders = ['آیدی کاربر', 'نام', 'نام خانوادگی', 'موبایل', 'آیدی محصولات مرتبط با کلاس آنلاین'];
        $excelData = [];
        foreach ($usersWithProducts as $userId => $productIds) {
            $user = User::find($userId);
            $excelData[] = [$user->id, $user->firstName, $user->lastName, $user->mobile, $productIds];
        }
        $filePath = 'liveConductorReport/'.now()->timestamp.'.xlsx';
        Excel::store(new DefaultClassExport(collect($excelData), $excelHeaders), $filePath, $disk);
        $fileUrl = Storage::disk($disk)->download($filePath);
        return response()->json(['file_url' => $fileUrl]);
    }
}
