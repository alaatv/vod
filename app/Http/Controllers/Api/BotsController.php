<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Http\Resources\UserFor3A;
use App\Jobs\InsertBatchOrders;
use App\Jobs\insertBonyadEhsanUsers;
use App\Jobs\InsertExcel;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Notifications\SendVerificationCodeToUnknownNumber;
use App\Traits\Helper;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Validator;

class BotsController extends Controller
{
    use Helper;

    /**
     * BotsController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:100,1')->only('sendCodeToUnknownNumber');
        $this->middleware('permission:'.config('constants.INSERT_BATCH_ORDERS'), ['only' => 'queueBatchInsertJob',]);
        $this->middleware('permission:'.config('constants.SHOW_USER_ACCESS'), ['only' => 'getUserData',]);
        $this->middleware('role:'.config('constants.ROLE_ADMIN'), ['only' => 'queueExcelInsertion',]);
    }

    public function queueBatchInsertJob(Request $request)
    {
        $request->validate([
            'coupons_id' => ['required', 'integer', 'min:1', 'exists:coupons,id,deleted_at,NULL']
        ]);

        $authUser = $request->user();
        $array = $request->get('pk');
        $couponId = $request->get('coupons_id');
        $rules = [
            'pk' => ['required', 'array']
        ];

        if ($authUser->hasRole(config('constants.ROLE_PUBLIC_RELATION_MANAGER'))) {
            $rules['products'] = ['required', 'array'];
            Validator::make($request->all(), $rules)->validate();

            $products = Product::query()->whereIn('id', $request->get('products'))->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'message' => 'محصولات درخواست شده یافت نشد'
                ]);
            }

            if (empty($array)) {
                return response()->json([
                    'message' => 'کاربری ارسال نشده است'
                ]);
            }

            $coupon = Coupon::find($couponId);

            foreach ($array as $key => $item) {
                $schoolType = Arr::get($item, 12);
                if (!isset($schoolType)) {
                    continue;
                }

                switch ($schoolType) {
                    case 'دولتی':
                        $item[12] = 1;
                        break;
                    case 'نمونه دولتی':
                        $item[12] = 2;
                        break;
                    case 'هیات امنایی':
                        $item[12] = 3;
                        break;
                    case 'غیر دولتی':
                        $item[12] = 4;
                        break;
                    case 'سمپاد':
                    case 'تیزهوشان':
                    case 'استعدادهای درخشان':
                        $item[12] = 5;
                        break;
                    case 'شاهد':
                        $item[12] = 6;
                        break;
                    default:
                        $item[12] = null;
                        break;
                }

                $array[$key] = $item;

            }

            $request->offsetSet('pk', $array);
            $this->dispatch(new InsertBatchOrders($array, $authUser, $products, $coupon));
            return response()->json(['message' => 'درخواست عملیات با موفقیت در صف قرار گرفت']);
        }

        if (!$authUser->hasRole(config('constants.ROLE_KOMITE_STAFF'))) {
            return response()->json(['message' => 'شما اجازه انجام این عملیات را ندارید']);
        }

        Validator::make($request->all(), $rules)->validate();

        if (empty($array)) {
            return response()->json([
                'message' => 'کاربری ارسال نشده است'
            ]);
        }


        $this->dispatch(new insertBonyadEhsanUsers($array, $authUser));
        return response()->json(['message' => 'درخواست عملیات با موفقیت در صف قرار گرفت']);
    }

    public function queueExcelInsertion(Request $request)
    {
        dispatch(new InsertExcel($request->get('data'), $request->user()));
        return response()->json(['message' => 'درخواست عملیات با موفقیت در صف قرار گرفت']);
    }

    public function sendCodeToUnknownNumber(Request $request)
    {
        Validator::make($request->all(), [
            'code' => 'digits:5',
            'number' => ['digits:11', 'phone:AUTO,IR'],
            'h' => 'string',
        ])->validate();

        $hashVerification = $this->verifyHash($request->get('code'), $request->get('number'), $request->bearerToken(),
            $request->get('h'));

        if (!$hashVerification) {
            return myAbort(Response::HTTP_UNAUTHORIZED, 'Unauthorized h');
        }

        Notification::route('mobile',
            $request->get('number'))->notify(new SendVerificationCodeToUnknownNumber($request->get('code')));

        return response()->json(['message' => 'The code was sent successfully']);
    }

    private function verifyHash(?string $code, ?string $number, ?string $token, ?string $hash): bool
    {
        return hash('sha256', $number.$code.$token.'thebluecat') == $hash;
    }

    public function sendCodeToUnknownNumberPen(Request $request)
    {
        Validator::make($request->all(), [
            'code' => 'digits:6',
            'number' => ['digits:11', 'phone:AUTO,IR'],
        ])->validate();

        Notification::route('mobile',
            $request->get('number'))->notify(new SendVerificationCodeToUnknownNumber($request->get('code')));

        return response()->json(['message' => 'The code was sent successfully']);
    }

    public function getUserData(Request $request, User $user)
    {
        return new UserFor3A($user);
    }
}
