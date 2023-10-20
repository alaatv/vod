<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\UserUpdateProvinceCitySearch;
use App\Classes\Uploader\Uploader;
use App\Classes\UserFavored;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\NationalPhotoUploadRequest;
use App\Http\Requests\UserExamSaveRequest;
use App\Http\Requests\UserFavoredRequest;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserIsPermittedToPurchaseRequest;
use App\Http\Requests\UserOrdersRequest;
use App\Http\Resources\Admin\ProfileMetaDataResource;
use App\Http\Resources\EntekhabReshteResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\Transaction as TransactionResource;
use App\Http\Resources\User as UserResource;
use App\Models\Gender;
use App\Models\Ostan;
use App\Models\Product;
use App\Models\Shahr;
use App\Models\User;
use App\Repositories\GradeRepo;
use App\Repositories\MajorRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductRepository;
use App\Services\OrderService;
use App\Services\UserService;
use App\Traits\Helper;
use App\Traits\RequestCommon;
use App\Traits\UserCommon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Validator;

class UserController extends Controller
{
    use RequestCommon;
    use UserCommon;
    use Helper;

    /**
     * UserController constructor.
     */
    public function __construct(public UserService $userService)
    {
        $this->middleware('permission:'.config('constants.SHOW_USER_BY_CREDENTIALS'), ['only' => 'getInfo']);
        $this->middleware('permission:'.config('constants.FIX_UNKNOWN_CITY_ADMIN_PANEL_ACCESS'),
            ['only' => 'unknownUsersCityIndex']);
    }

    public function index()
    {
        return response()->json();
    }

    /**
     * API Version 2
     *
     * @param  EditUserRequest  $request
     * @param  User|null  $user
     *
     * @return ResponseFactory|JsonResponse|Response|void
     */
    public function updateV2(EditUserRequest $request, User $user = null)
    {
        //ToDo : Should be removed after dropping city column
        if ($request->has('city')) {
            $shahr = Shahr::query()->where('name', $request->get('city'))->first();
            if (isset($shahr)) {
                $user->shahr_id = $shahr->id;
                $request->offsetSet('shahr_id', $shahr->id);
            }

            $request->offsetUnset('city');
            $request->offsetUnset('province');
        }

        $authenticatedUser = $request->user('alaatv');
        if ($user === null) {
            $user = $authenticatedUser;
        }

        if ($user->id != $authenticatedUser->id) {
            return response()->json([
                'message' => 'Forbidden.',
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $user->fillByPublic($request->all());

            $file = $this->getRequestFile($request->all(), 'photo');
            if ($file !== false) {
                $storePicResult = $this->storePhotoOfUser($user, $file);
                if (isset($storePicResult)) {
                    $user->photo = $storePicResult;
                }
            }
        } catch (FileNotFoundException $e) {
            return response([
                'error' => [
                    'text' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($user->checkUserProfileForLocking()) {
            $user->lockHisProfile();
        }

        if ($user->update()) {
            Cache::tags('user_'.$user->id)->flush();
            return (new UserResource($user))->response();
        }
        return response()->json([
            'message' => 'Database error on updating user',
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }

    /**
     * Update the specified resource in storage.
     * Note: Requests to this method must pass \App\Http\Middleware\trimUserRequest middle ware
     *
     * @param  EditUserRequest  $request
     * @param  User  $user
     *
     * @return array|Response
     */
    public function update(EditUserRequest $request, User $user = null)
    {
        $authenticatedUser = $request->user('api');
        if ($user === null) {
            $user = $authenticatedUser;
        }
        try {
            $user->fillByPublic($request->all());
            $file = $this->getRequestFile($request->all(), 'photo');
            if ($file !== false) {
                $this->storePhotoOfUser($user, $file);
            }
        } catch (FileNotFoundException $e) {
            return response([
                'error' => [
                    'text' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($user->checkUserProfileForLocking()) {
            $user->lockHisProfile();
        }

        if ($user->update()) {

            $message = 'User profile updated successfully';
            $status = Response::HTTP_OK;
        } else {
            $message = 'Database error on updating user';
            $status = Response::HTTP_SERVICE_UNAVAILABLE;
        }

        if ($status == Response::HTTP_OK) {
            $response = [
                'user' => $user,
                'message' => $message,
            ];
        } else {
            $response = [
                'error' => [
                    'code' => $status,
                    'message' => $message,
                ],
            ];
        }

        Cache::tags('user_'.$user->id)->flush();

        return response($response);
    }

    public function show(Request $request, User $user)
    {
        $authenticatedUser = $request->user('api');

        if ($authenticatedUser->id == $user->id) {
            return response($user);
        }
        return response([
            'error' => [
                'code' => Response::HTTP_FORBIDDEN,
                'message' => 'UnAuthorized',
            ],
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     * @param  User  $user
     *
     * @return ResponseFactory|JsonResponse|Response
     */
    public function showV2(Request $request, User $user)
    {
        $authenticatedUser = $request->user('api');

        if ($authenticatedUser->id != $user->id) {
            return response([
                'error' => [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'UnAuthorized',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        $user->editProfileUrl = $this->getEncryptedProfileEditUrl(encrypt(['user_id' => $user->id]));

        return (new UserResource($user))->response();
    }

    /**
     * Gets a list of user orders
     *
     * @param  Request  $request
     *
     * @param  User  $user
     *
     * @return ResponseFactory|JsonResponse|Response
     */
    public function userOrders(Request $request, User $user)
    {
        /** @var User $user */
        $authenticatedUser = $request->user('api');

        if ($authenticatedUser->id != $user->id && !$authenticatedUser->isAbleTo(config('constants.LIST_USER_ORDERS'))) {
            return response([
                'error' => [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'UnAuthorized',
                ],
            ]);
        }

        $orders = $user->getClosedOrders($request->get('orders', 1));

        return response()->json($orders);
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     * @param  User  $user
     *
     * @return OrderResource|ResourceCollection|ResponseFactory|Response
     */
    public function userOrdersV2(UserOrdersRequest $request, User $user)
    {
        /** @var User $user */
        $authenticatedUser = $request->user('api');

        if ($authenticatedUser->id != $user->id && !$authenticatedUser->isAbleTo(config('constants.LIST_USER_ORDERS'))) {
            return response([
                'error' => [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'UnAuthorized',
                ],
            ]);
        }

        $orders = $user->getClosedOrdersForAPIV2($request->get('orders', 1));
        $orderId = $request->input('search');
        if (strlen($orderId) > 0) {
            $orders = $orders->where('id', $orderId);
        }
        $paymentStatusesId = $request->get('paymentStatuses');
        if (isset($paymentStatusesId)) {
            $orders = OrderRepo::paymentStatusFilter($orders, $paymentStatusesId);
        }
        $createdSinceDate = $request->get('since');
        $createdTillDate = $request->get('till');

        if (strlen($createdSinceDate) > 0 || strlen($createdTillDate) > 0) {
            $orders = $this->timeFilterQuery(list: $orders, sinceDate: $createdSinceDate, tillDate: $createdTillDate);
        }

        return OrderResource::collection($orders);
    }

    /**
     * @param  Request  $request
     * @param  User  $user
     *
     * @return Application|ResponseFactory|ResourceCollection|Response
     */
    public function userTransactionsV2(Request $request, User $user)
    {
        /** @var User $user */
        $authenticatedUser = $request->user('api');

        if ($authenticatedUser->id != $user->id) {
            return response([
                'error' => [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'UnAuthorized',
                ],
            ]);
        }

        $transactions = $user->getTransactionsForAPIV2($request->get('transactions', 1));

        return TransactionResource::collection($transactions);
    }

    public function userInstallmentsV2(Request $request, User $user)
    {
        /** @var User $user */
        $authenticatedUser = $request->user('api');

        if ($authenticatedUser->id != $user->id) {
            return response([
                'error' => [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'UnAuthorized',
                ],
            ]);
        }

        $installments = $user->getInstallmentsForAPIV2($request->get('installments', 1));

        return TransactionResource::collection($installments);
    }

    public function getAuth2Profile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->fullName,
            'email' => md5($user->mobile).'@sanatisharif.ir',

        ]);
    }

    public function getInfo(Request $request)
    {
        Validator::make($request->all(), [
            'mobile' => ['required'],
            'nationalCode' => ['required'],
        ])->validate();

        $user =
            User::where('mobile', $request->get('mobile'))->where('nationalCode',
                $request->get('nationalCode'))->first();

        if (!isset($user)) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return (new UserResource($user))->response();
    }

    public function getUserFormData()
    {
        $data = Cache::remember('getUserFormData-data', config('constants.CACHE_600'), function () {
            $allOstan = Ostan::all();
            $allShahr = Shahr::allDistrictZero();
            $allMajor = MajorRepo::getBasicMajors()->get();
            $allGrade = GradeRepo::get3AGrades()->get();
            $allGender = Gender::all();
            $data = [
                'provinces' => $allOstan,
                'cities' => $allShahr,
                'majors' => $allMajor,
                'grades' => $allGrade,
                'genders' => $allGender,
            ];
            return $data;
        });

        return (new ProfileMetaDataResource($data))->response();
    }

    public function unknownUsersCityIndex(
        UserIndexRequest $request,
        UserUpdateProvinceCitySearch $userUpdateProvinceCitySearch
    ) {
        $result = User::with('shahr')->whereNull('shahr_id')
            ->where(function ($q) {
                $q->whereNotNull('province')
                    ->orWhereNotNull('city');
            })->orderBy('created_at', 'desc');

        $result =
            $result->paginate($request->get('length'), ['*'], 'unknownUsersCityPage',
                $request->get('unknownUsersCityPage'));

        $result->each(function ($item) {
            return $item->append('provinces');
        });

        return $result;
    }

    public function storeNationalCardPhoto(NationalPhotoUploadRequest $request)
    {
        $user = $request->user();
        $file = $request->file('photo');
        if ($file) {
            $user->kartemeli = $this->storePhotoOfKartemeli($user, $file);
            $user->updateWithoutTimestamp();
        }
        $url = str_replace('//https://nodes.alaatv.com', 'https://nodes.alaatv.com', $user->kartemeli);

        return response()->json(['data' => ['url' => $url]]);
    }

    public function getNationalCardPhoto()
    {
        $user = auth()->user();
        $nationalCardPath = $user->getRawOriginal('kartemeli');
        if ($nationalCardPath) {
            $url = Uploader::url(config('disks.KARTE_MELI_IMAGE_MINIO'), $nationalCardPath, false);
            return response()->json([
                'data' => [
                    'url' => $url,
                ],
            ]);
        }
        return response()->json([
            'data' => [
                'url' => null,
            ],
        ]);
    }

    public function checkUserAccess()
    {
        return response()->json(['data' => ['id' => auth()->id(), 'access' => true]]);
    }

    public function userFavored(UserFavoredRequest $request, UserFavored $userFavored)
    {
        return $userFavored
            ->setLimitForPaginate($request->query('limit') ?? 15)
            ->setSearch($request->query('search'))
            ->setProductId($request->query('product_id'))
            ->setContentSetTitle($request->query('contentset_title'))
            ->setContentTypeIds($request->query('content_type_ids'))
            ->get();
    }

    public function examSave(UserExamSaveRequest $request)
    {
        auth()->user()->exams()->attach($request->input('exam_id'));
        return response()->json([
            'message' => 'exam successfully saved for user.',
        ]);
    }

    public function hasPurchased(Request $request)
    {
        Validator::make($request->all(), [
            'products' => ['required', 'array'],
        ])->validate();

        $products = ProductRepository::getProductsById($request->get('products'));

        return response()->json([
            'data' => $products->get()->map(fn($product) => [
                'id' => $product->getKey(),
                'is_purchased' => $product->isPurchased,
            ])->toArray(),
        ]);
    }

    public function isPermittedToPurchase(Product $product, OrderService $orderService): JsonResponse
    {
        if (!$product->isActive()) {
            return myAbort(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                'این گزینه در حال حاضر غیرفعال می باشد');
        }
        $result = match ($product->id) {
            1240 => $this->userService->checkUserCanGetEntekhabReshteAbrisham1(
                [
                    Product::RAHE_ABRISHAM99_PACK_TAJROBI,
                    Product::RAHE_ABRISHAM99_PACK_RIYAZI,
                ]
            ),
            1239 => $this->userService->checkUserCanGetEntekhabReshteAbrishamPro(
                [
                    Product::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI,
                    Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
                    Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
                    771,
                    770,
                    769,
                ]
            ),
            default => true
        };
        if ($result) {
            $order = $orderService->createOpenOrderWithBasicOrderProduct(auth()->id(), $product->id);
            return response()->json([
                'data' => [
                    'order_id' => $order->id,
                ],
            ]);
        }
        return myAbort(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
            'شما مجاز به انتخاب این گزینه نیستید!');
    }

    public function getEntekhabReshte(Request $request)
    {
        $authUser = $request->user();
        if ($request->has('user_id')) {
            if ($authUser->isAbleTo(config('constants.GET_USER_ENTEKHAB_RESHTE'))) {
                return new EntekhabReshteResource(User::find($request->query('user_id'))->entekhabReshte);
            } else {
                return myAbort(Response::HTTP_FORBIDDEN, 'دسترسی ندارید');
            }
        }
        return new EntekhabReshteResource($authUser->entekhabReshte);
    }
}
