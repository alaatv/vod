<?php

namespace App\Http\Controllers\Api\BonyadEhsan\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BonyadEhsan\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_LIST_ORDER'))->only(['index']);
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_REMOVE_ORDER'))->only(['destroy']);
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $creatorsFilter = $user->hasPermission(config('constants.BONYAD_EHSAN_FILTER_ORDER_CREATOR')) ?
            $request->get('creators', []) :
            [$user->id];

        $orders = OrderRepo::filterOrdersBaseCoupon([
            'id' => [
                'value' => Coupon::BONYAD_EHSAN_COUPON
            ]
        ])
            ->with(['orderproducts', 'activities' => fn($query) => $query->forEvent('created'), 'user'])
            ->createdBy($creatorsFilter)
            ->paginate();

        return OrderResource::collection($orders);
    }

    public function destroy(Order $order)
    {
        if ($order->delete()) {
            return response()->json(Response::HTTP_OK);
        }

        return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error');
    }
}
