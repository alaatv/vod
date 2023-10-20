<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReferralCodeBatchStoreRequest;
use App\Http\Requests\ReferralCodeSharedRequest;
use App\Http\Resources\OrderTransactionCommissionResource;
use App\Http\Resources\ReferralCodeResource;
use App\Models\Billing;
use App\Models\Orderproduct;
use App\Models\ReferralCode;
use App\Models\User;
use App\Notifications\ReferralCodeAssigned;
use App\Notifications\ReferralCodeGenerate;
use App\Repositories\Loging\ActivityLogRepo;
use App\Traits\User\ReferralRequestTrait;
use Illuminate\Http\Request;

class ReferralCodesController extends Controller
{
    use ReferralRequestTrait;

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.GENERATE_GIFT_CARD_PANEL'), ['only' => ['batchStore'],]);
    }

    public function index(Request $request)
    {
        $builder = ReferralCode::whereHas('referralRequest', function ($query) {
            $query->where('owner_id', auth()->id());
        });
        $builder->when(
            $request->has('is_assigned_unused') && $request->query('is_assigned_unused') == 1,
            function ($query) use ($request) {
                $query->assigned(1)->used(0);
            }
        );
        $builder->when(
            $request->has('is_used_and_paid') && $request->query('is_used_and_paid') == 1,
            function ($query) use ($request) {
                $query->used(1)->sold();
            }
        );
        $builder->when(
            $request->has('is_used_and_unpaid') && $request->query('is_used_and_unpaid') == 1,
            function ($query) use ($request) {
                $query->used(1)->notSold();
            }
        );
        $builder->when(
            $request->has('is_unassigned') && $request->query('is_unassigned') == 1,
            function ($query) use ($request) {
                $query->assigned(0)->used(0);
            }
        );
        $referralCodes = $builder->paginate(5);
        return ReferralCodeResource::collection($referralCodes);
    }

    public function show(ReferralCode $referralCode)
    {
        return new ReferralCodeResource($referralCode);
    }

    public function assign(ReferralCodeSharedRequest $request, ReferralCode $referralCode)
    {
        $assign = $request->get('assign');
        $referralCode->update([
            'isAssigned' => $assign,
            'assignor_id' => $request->user()->id,
            'assignor_device_id' => config('constants.DEVICE_TYPE_DESKTOP'),
        ]);
        $viaSMS = $request->get('via_sms', 0);
        if ($viaSMS) {
            $request->user()->notify(new ReferralCodeAssigned($referralCode->id));
        }
        return response()->json(['data' => $referralCode->isAssigned]);
    }

    public function showImage(ReferralCode $referralCode)
    {
        $giftCartCode = $referralCode->code;
        return view('pages/showGiftCard', compact('giftCartCode'));
    }

    public function indexOrderproducts(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $orderProducts =
            Billing::where('is_donate', 0)->wherehas('order.referralCode', function ($query) use ($perPage) {
                $query->wherehas('referralRequest', function ($query) {
                    $query->where('owner_id', auth()->id());
                });
            })->wherehas('order', function ($query) use ($perPage) {
                $query->paidAndClosed();
            })->with('order.user', 'order.referralCode', 'order.transactions', 'product')->paginate($perPage);

        return OrderTransactionCommissionResource::collection($orderProducts);
    }

    public function indexNoneProfitableOrderproducts(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $orderProducts = Orderproduct::with([
            'order' => function ($query) {
                $query->paidAndClosed();
            },
            'order.referralCode' => function ($query) {
                $query->where('owner_id', auth()->id());
            },
            'order.transactions',
            'product',
            'order.user',
        ])->whereHas('order', function ($query) {
            $query->paidAndClosed()
                ->whereHas('referralCode', function ($query) {
                    $query->where('owner_id', auth()->id());
                });
        })->get();
        $walletPaidOrderProducts = $orderProducts->filter(
            fn($orderProduct) => $orderProduct->order
                ->transactions->filter(fn($transaction) => $transaction->wallet_id != null)
                ->count()
        );
        $underLimitCostOrderProducts = $orderProducts->filter(function ($orderProduct) {
            return $orderProduct->order->totalCost() < 100000;
        });
        $noneProfitableOrderProducts = $walletPaidOrderProducts->merge($underLimitCostOrderProducts);
        return OrderTransactionCommissionResource::collection($noneProfitableOrderProducts->paginate($perPage));
    }

    public function batchStore(ReferralCodeBatchStoreRequest $request)
    {
        $inputs = $request->validated();
        $user = User::where('mobile', $inputs['mobile'])->where('nationalCode', $inputs['nationalCode'])->first();
        if (!isset($user)) {
            $user = User::create([
                'firstName' => $inputs['firstName'],
                'lastName' => $inputs['lastName'],
                'mobile' => $inputs['mobile'],
                'nationalCode' => $inputs['nationalCode'],
                'password' => bcrypt($inputs['nationalCode']),
                'userstatus_id' => config('constants.USERBON_STATUS_ACTIVE'),
            ]);
        }
        $referralRequest = $this->createReferralRequest($user, $inputs['discounttype_id'], $inputs['number_of_codes'],
            $inputs['commission']);
        $this->createReferralCodes($referralRequest, $inputs['number_of_codes']);
        ActivityLogRepo::referralCodesGenerated(auth()->user(), $referralRequest);
        $user->notify(new ReferralCodeGenerate($referralRequest, route('web.giftCards')));
        return new \App\Http\Resources\User($user);
    }
}
