<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\UserUpdateProvinceCitySearch;
use App\Classes\SEO\SeoDummyTags;
use App\Classes\Uploader\Uploader;
use App\Classes\UserFavored;
use App\Events\Authenticated;
use App\Exports\DefaultClassExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditOrderRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\GroupRegistrationRequest;
use App\Http\Requests\InsertContactRequest;
use App\Http\Requests\InsertPhoneRequest;
use App\Http\Requests\MarketingReportRequest;
use App\Http\Requests\NationalPhotoUploadRequest;
use App\Http\Requests\UserExamSaveRequest;
use App\Http\Requests\UserFavoredRequest;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserOrdersRequest;
use App\Http\Resources\Admin\ProfileMetaDataResource;
use App\Http\Resources\EntekhabReshteResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\Transaction as TransactionResource;
use App\Http\Resources\User as UserResource;
use App\Imports\UsersOrderImport;
use App\Jobs\GroupRegistrationJob;
use App\Models\Afterloginformcontrol;
use App\Models\Bloodtype;
use App\Models\Contact;
use App\Models\Contacttype;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\Gender;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Order;
use App\Models\Ostan;
use App\Models\Phonetype;
use App\Models\Product;
use App\Models\Region;
use App\Models\Relative;
use App\Models\Shahr;
use App\Models\SmsUser;
use App\Models\User;
use App\Repositories\GradeRepo;
use App\Repositories\MajorRepo;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductRepository;
use App\Services\OrderService;
use App\Services\UserService;
use App\Traits\Helper;
use App\Traits\MetaCommon;
use App\Traits\RequestCommon;
use App\Traits\UserCommon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    use RequestCommon;
    use UserCommon;
    use Helper;
    use MetaCommon;

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
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|JsonResponse|Response
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
            ], ResponseAlias::HTTP_FORBIDDEN);
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
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
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
        ], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
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

    /**
     * @throws ValidationException
     */
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
            return myAbort(ResponseAlias::HTTP_FORBIDDEN,
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
        return myAbort(ResponseAlias::HTTP_FORBIDDEN,
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

    public function userProductFiles(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'دسترسی ندارید'], 401);
        }
        return response()->json(['user' => $request->user()], 200);
    }

    /**
     * @throws Exception
     */
    public function submitKonkurResult(Request $request)
    {
        $setting = alaaSetting();
        $url = $request->url();
        $title = 'ثبت رتبه کنکور 1401 | آلاء';
        $this->generateSeoMetaTags(new SeoDummyTags($title, 'ثبت رتبه کنکور 1401 | آلاء', $url,
            $url, route('image', [
                'category' => '11',
                'w' => '100',
                'h' => '100',
                'filename' => $setting->setting->site->siteLogo,
            ]), '100', '100', null));

        $user = $request->user();
        $userCompletion = isset($user) ? $user->info['completion'] : 0;

        $event = Event::name('konkur1401')->first();
        $userKonkurResult = isset($user) ? $user->eventresults->where('event_id', $event->id)->first() : null;

        $sideBarMode = 'closed';
        $pageType = 'sabteRotbe';
        $allOstan = collect();
        $allShahr = collect();
        $year = 1401;

        $ad = [
            'enable' => false,
            'link' => null,
            'name' => '',
            'id' => '4k-banner',
            'creative' => '',
            'position' => 'landing-nahayi-23-bottom-section',
            'imgMobileSrc' => 'https://nodes.alaatv.com/upload/sabte_rotbe_1400_mob.jpg',
            'imgMobileWidth' => '480',
            'imgMobileHeight' => '241',
            'imgDesktopSrc' => 'https://nodes.alaatv.com/upload/sabte_rotbe_1400_desk.jpg',
            'imgDesktopWidth' => '1183',
            'imgDesktopHeight' => '220'
        ];

        $regions = Region::all();
        $majors = Major::all()->except([4]); // 4 = id of علوم و معارف اسلامی

        return response()->json([
            'user' => $user,
            'event' => $event,
            'userKonkurResult' => $userKonkurResult,
            'sideBarMode' => $sideBarMode,
            'userCompletion' => $userCompletion,
            'pageType' => $pageType,
            'allOstan' => $allOstan,
            'allShahr' => $allShahr,
            'year' => $year,
            'ad' => $ad,
            'regions' => $regions,
            'majors' => $majors
        ]);
    }

    public function marketingReport(MarketingReportRequest $request)
    {
        $purchasedProducts = $request->input('purchased_products');
        $haveBought = $request->input('have_bought');
        $sinceDate = $request->input('sinceDate');
        $tillDate = $request->input('tillDate');
        $users = User::with([
            'orders' => function ($query) use ($haveBought, $sinceDate, $tillDate) {
                return $query->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                    ->whereIn('paymentstatus_id', [config('constants.PAYMENT_STATUS_PAID')])
                    ->where('completed_at', '>=', $sinceDate)
                    ->where('completed_at', '<', $tillDate)
                    ->whereHas('normalOrderproducts', function ($query) use ($haveBought) {
                        $query->whereIn('product_id', $haveBought);
                    });
            },
        ])
            ->whereHas('orders', function ($query) use ($purchasedProducts, $sinceDate, $tillDate) {
                $query->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                    ->whereIn('paymentstatus_id', [config('constants.PAYMENT_STATUS_PAID')])
                    ->where('completed_at', '>=', $sinceDate)
                    ->where('completed_at', '<', $tillDate)
                    ->whereHas('transactions', function ($query) {
                        $query->where('cost', '>', 0);
                        $query->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'));
                        $query->whereDoesntHave('wallet', function ($query) {
                            $query->where('wallettype_id', config('constants.WALLET_TYPE_GIFT'));
                        });
                    })
                    ->whereHas('normalOrderproducts', function ($query) use ($purchasedProducts) {
                        $query->whereIn('product_id', $purchasedProducts);
                    });
            })->whereDoesntHave('roles')
            ->get();
        $hekmatCoupons = Coupon::select('id')->whereIn('code', ['hekmat50', 'hekmat40'])
            ->pluck('id')->toArray();
        $collect = [];
        foreach ($users as $key => $user) {
            $collect[$key]['fullName'] = $user->fullName;
            $collect[$key]['order_completed_at'] = implode(' , ', $user->orders->map(function ($order) {
                return $order->convertDate($order->completed_at, 'toJalali');
            })->toArray());
            $collect[$key]['mobile'] = $user->mobile;
            $collect[$key]['products'] =
                implode(' , ', $user->orders->map(function ($order) use ($haveBought) {
                    return $order->normalOrderproducts
                        ->whereIn('product_id', $haveBought)
                        ->map(function ($orderproduct) {
                            return $orderproduct->product->name;
                        });
                })->flatten()->toArray());

            $collect[$key]['buy_with_hekmat_coupon'] =
                implode(' , ', $user->orders->map(function ($order) use ($hekmatCoupons) {
                    return in_array($order->coupon_id, $hekmatCoupons) ? 'بله' : 'خیر';
                })->toArray());
        }
        $disk = config('disks.MINIO_UPLOAD_EXCEL');
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "marketing_report_$now.xlsx";
        $headers =
            [
                'نام و نام خانوادگی', 'تاریخ ثبت سفارش', 'شماره موبایل', 'عنوان محصولات خریداری شده',
                'استفاده از حکمت کارت ؟'
            ];
        Excel::store(new DefaultClassExport(collect($collect), $headers), 'excel/'.$fileName, $disk);
        $file = Uploader::url($disk, $fileName);
        return response()->json(['marketing_report_file' => $file], ResponseAlias::HTTP_OK);
    }

    public function completeRegister(Request $request)
    {
        $targetUrl = $request->has('redirectTo') ? $request->get('redirectTo') : action('Web\IndexPageController');

        if ($request->user()->completion('afterLoginForm') == 100) {
            return response()->json(['message' => 'User registration already completed.'], 200);
        }

        $previousPath = url()->previous();
        $formByPass = strcmp($previousPath, route('login')) != 0;
        $note = $formByPass ? 'برای استفاده از این خدمت سایت لطفا اطلاعات زیر را تکمیل نمایید' : 'برای ورود به سایت لطفا اطلاعات زیر را تکمیل نمایید';

        $formFields = Afterloginformcontrol::getFormFields();
        $tables = [];
        foreach ($formFields as $formField) {
            if (strpos($formField->name, '_id')) {
                $tableName = str_replace('_id', 's', $formField->name);
                $tables[$formField->name] = DB::table($tableName)->pluck('name', 'id');
            }
        }

        return response()->json(compact('formFields', 'note', 'formByPass', 'tables'), 200);
    }

    public function groupRegistration(GroupRegistrationRequest $request)
    {
        $file = Arr::get($request, 'file');
        $products = Arr::get($request, 'productIds', []);
        $giftProducts = Arr::get($request, 'giftIds', []);
        $discount = Arr::get($request, 'discount', 0);
        $paymentStatusId = Arr::get($request, 'paymentStatusId');
        $orderStatusId = Arr::get($request, 'orderStatusId');

        (new UsersOrderImport())->import($file);
        $rows = UsersOrderImport::$rows;

        dispatch(new GroupRegistrationJob($request->user(), $rows, $products, $giftProducts, $discount,
            $paymentStatusId, $orderStatusId));

        return response()->json(['message' => 'عملیات با موففیت انجام شد']);
    }

    public function information(User $user)
    {
        $data = [];

        $validOrders = $user->orders()
            ->whereHas('orderproducts', function ($q) {
                $q->whereIn('product_id', config('constants.ORDOO_GHEIRE_HOZOORI_NOROOZ_97_PRODUCT'))
                    ->orWhereIn('product_id', config('constants.ORDOO_HOZOORI_NOROOZ_97_PRODUCT'))
                    ->orWhereIn('product_id', [199, 202]);
            })
            ->whereIn('orderstatus_id', [config('constants.ORDER_STATUS_CLOSED')])
            ->get();

        if ($validOrders->isEmpty()) {
            return response()->json(['message' => 'No valid orders found'], ResponseAlias::HTTP_NOT_FOUND);
        }

        $unPaidOrders = $validOrders;
        $paidOrder = $validOrders->whereIn('paymentstatus_id', [
            config('constants.PAYMENT_STATUS_PAID'),
            config('constants.PAYMENT_STATUS_INDEBTED'),
        ])->get();

        $order = $unPaidOrders->first();

        if (is_null($order)) {
            $order = $paidOrder->first();
        }

        if (!isset($order)) {
            return response()->json(['message' => 'Order not found'], ResponseAlias::HTTP_FORBIDDEN);
        }

        $orderproduct = $order->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->first();

        $product = $orderproduct->product;
        $data['userProduct'] = $product->grandParent ? $product->grandParent->name : $product->name;

        $simpleContact = Contacttype::where('name', 'simple')->first();
        $mobilePhoneType = Phonetype::where('name', 'mobile')->first();
        $parents = Relative::whereIn('name', ['father', 'mother'])->get();
        $parentsNumber = [];
        foreach ($parents as $parent) {
            $parentContacts = $user->contacts->where('relative_id', $parent->id)->where('contacttype_id',
                $simpleContact->id);
            if ($parentContacts->isEmpty()) {
                continue;
            }
            $parentContact = $parentContacts->first();
            $parentMobiles = $parentContact->phones->where('phonetype_id', $mobilePhoneType->id)->sortBy('priority');
            if ($parentMobiles->isEmpty()) {
                continue;
            }
            $parentMobile = $parentMobiles->first()->phoneNumber;
            $parentsNumber[$parent->name] = $parentMobile;
        }

        $data['parentsNumber'] = $parentsNumber;
        $data['majors'] = Arr::sortRecursive(Major::pluck('name', 'id')->toArray());
        $data['genders'] = Arr::sortRecursive(Gender::pluck('name', 'id')->toArray());
        $data['bloodTypes'] = Arr::sortRecursive(Bloodtype::pluck('name', 'id')->toArray());
        $data['grades'] = Arr::sortRecursive(Grade::pluck('displayName', 'id')->toArray());
        $data['orderFiles'] = $order->files;

        $lockedFields = [];
        if ($user->lockProfile) {
            $lockedFields = $user->returnLockProfileItems();
        }

        $completionFields = [];
        $completionPercentage = 0;
        if (in_array($product->id, config('constants.ORDOO_HOZOORI_NOROOZ_97_PRODUCT'))) {
            $completionFields = $user->returnCompletionItems();
            $completionFieldsCount = count($completionFields);
            $completionPercentage = (int) $user->completion('completeInfo');
        } else {
            $completionFields = array_diff($user->returnCompletionItems(), $user->returnMedicalItems());
            $completionFieldsCount = count($completionFields);
            $completionPercentage = (int) $user->completion('custom', $completionFields);
        }

        $completedFieldsCount = (int) ceil(($completionPercentage * $completionFieldsCount) / 100);
        if ($data['orderFiles']->isNotEmpty()) {
            $completedFieldsCount++;
        }
        $completionFieldsCount++;

        if (isset($order->customerExtraInfo)) {
            $customerExtraInfo = json_decode($order->customerExtraInfo);
            foreach ($customerExtraInfo as $item) {
                if (isset($item->info) && strlen(trim($item->info)) > 0) {
                    $completedFieldsCount++;
                }
                $completionFieldsCount++;
            }
        }

        if (isset($parentsNumber['father'])) {
            $completedFieldsCount++;
        }
        $completionFieldsCount++;

        if (isset($parentsNumber['mother'])) {
            $completedFieldsCount++;
        }
        $completionFieldsCount++;

        $completionPercentage = (int) (($completedFieldsCount / $completionFieldsCount) * 100);

        if ($completionPercentage == 100 && $user->completion('lockProfile') == 100) {
            $user->lockHisProfile();
            $user->updateWithoutTimestamp();
        }

        $data['lockedFields'] = $lockedFields;
        $data['completionPercentage'] = $completionPercentage;
        $data['customerExtraInfo'] = isset($order->customerExtraInfo) ? json_decode($order->customerExtraInfo) : null;

        return response()->json(['data' => $data], ResponseAlias::HTTP_OK);
    }

    public function completeInformation(
        User $user,
        Request $request,
        UserController $userController,
        PhoneController $phoneController,
        ContactController $contactController,
        OrderController $orderController
    ) {

        $request->offsetSet('phone', $this->convertToEnglish(preg_replace('/\s+/', '', $request->get('phone'))));
        $request->offsetSet('postalCode',
            $this->convertToEnglish(preg_replace('/\s+/', '', $request->get('postalCode'))));
        $parentMobiles = [
            'father' => $this->convertToEnglish(preg_replace('/\s+/', '', $request->get('parentMobiles')['father'])),
            'mother' => $this->convertToEnglish(preg_replace('/\s+/', '', $request->get('parentMobiles')['mother'])),
        ];
        $request->offsetSet('parentMobiles', $parentMobiles);

        $mapConvertToEnglish = [
            'school',
            'allergy',
            'medicalCondition',
            'diet',
            'introducer',
        ];
        foreach ($mapConvertToEnglish as $item) {
            $request->offsetSet($item, $this->convertToEnglish($request->get($item)));
        }

        $this->validate($request, [
            'photo' => 'image|mimes:jpeg,jpg,png|max:200',
            'file' => 'mimes:jpeg,jpg,png,zip,pdf,rar',
        ]);
        if ($request->user()->id != $user->id) {
            return response()->json(['message' => 'Unauthorized'], ResponseAlias::HTTP_FORBIDDEN);
        }

        if (!isset($requestData['order'])) {
            return response()->json(['message' => 'Order not specified'], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $orderId = $requestData['order'];
        $order = Order::findOrFail($orderId);

        if ($order->user_id != $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], ResponseAlias::HTTP_FORBIDDEN);
        }
        $editUserRequestMap = [
            'address',
            'postalCode',
            'school',
            'major_id',
            'grade_id',
            'gender_id',
            'shahr_id',
            'bloodtype_id',
            'phone',
            'allergy',
            'medicalCondition',
            'diet',
        ];
        foreach ($editUserRequestMap as $item) {
            if ($request->get($item) != 0) {
                $editUserRequest->offsetSet($item, $request->get($item));
            }
        }
        $userController->update($editUserRequest, $user);

        /**
         *
         */
        /**
         * Parent's basic info
         **/
        $simpleContact = Contacttype::where('name', 'simple')
            ->get()
            ->first();
        $mobilePhoneType = Phonetype::where('name', 'mobile')
            ->get()
            ->first();
        $parentsNumber = $request->get('parentMobiles');

        foreach ($parentsNumber as $relative => $mobile) {
            if (strlen(preg_replace('/\s+/', '', $mobile)) == 0) {
                continue;
            }
            $parent = Relative::where('name', $relative)
                ->get()
                ->first();
            $parentContacts = $user->contacts->where('relative_id', $parent->id)
                ->where('contacttype_id', $simpleContact->id);
            if ($parentContacts->isEmpty()) {
                $storeContactRequest = new InsertContactRequest();
                $storeContactRequest->offsetSet('name', $relative);
                $storeContactRequest->offsetSet('user_id', $user->id);
                $storeContactRequest->offsetSet('contacttype_id', $simpleContact->id);
                $storeContactRequest->offsetSet('relative_id', $parent->id);
                $storeContactRequest->offsetSet('isServiceRequest', true);
                $response = $contactController->store($storeContactRequest);
                if ($response->getStatusCode() == ResponseAlias::HTTP_OK) {
                    $responseContent = json_decode($response->getContent('contact'));
                    $parentContact = $responseContent->contact;
                }
            } else {
                $parentContact = $parentContacts->first();
            }
            if (!isset($parentContact)) {
                continue;
            }
            $parentContact = Contact::where('id', $parentContact->id)
                ->get()
                ->first();
            $parentMobiles = $parentContact->phones->where('phonetype_id', $mobilePhoneType->id)
                ->sortBy('priority');
            if ($parentMobiles->isEmpty()) {
                $storePhoneRequest = new InsertPhoneRequest();
                $storePhoneRequest->offsetSet('phoneNumber', $mobile);
                $storePhoneRequest->offsetSet('contact_id', $parentContact->id);
                $storePhoneRequest->offsetSet('phonetype_id', $mobilePhoneType->id);
                $response = $phoneController->store($storePhoneRequest);
                $response->getStatusCode();
            } else {
                $parentMobile = $parentMobiles->first();
                $parentMobile->phoneNumber = $mobile;
                $parentMobile->update();
            }
        }

        $updateOrderRequest = new EditOrderRequest();
        if ($request->hasFile('file')) {
            $updateOrderRequest->offsetSet('file', $request->file('file'));
        }
        /**
         * customerExtraInfo
         */
        $jsonConcats = '';
        $extraInfoQuestions = Arr::sortRecursive($request->get('customerExtraInfoQuestion'));
        $customerExtraInfoAnswers = $request->get('customerExtraInfoAnswer');
        foreach ($extraInfoQuestions as $key => $question) {
            $obj = new stdClass();
            $obj->title = $question;
            $obj->info = null;
            if (strlen(preg_replace('/\s+/', '', $customerExtraInfoAnswers[$key])) > 0) {
                $obj->info = $customerExtraInfoAnswers[$key];
            }
            if (strlen($jsonConcats) > 0) {
                $jsonConcats = $jsonConcats.','.json_encode($obj, JSON_UNESCAPED_UNICODE);
            } else {
                $jsonConcats = json_encode($obj, JSON_UNESCAPED_UNICODE);
            }
        }
        $customerExtraInfo = '['.$jsonConcats.']';
        $updateOrderRequest->offsetSet('customerExtraInfo', $customerExtraInfo);
        $orderController->update($updateOrderRequest, $order);

        return response()->json(['message' => 'اطلاعات با موفقیت ذخیره شد'], ResponseAlias::HTTP_OK);
    }

    public function userOrders(Request $request)
    {
        $user = $request->user();

        $ordersPageNum = $request->get('orders', 1);

        [
            $orders,
            $transactions,
            $instalments,
            $gateways,
            $credit,
        ] = [
            $user->getClosedOrders($ordersPageNum),
            $this->getUserTransactions($user),
            $this->getUserInstalments($user),
            $this->makeGatewayCollection(),
            $user->getTotalWalletBalance(),
        ];

        $orderIdString = $orders->pluck('id')->implode('-');
        $key = 'orders:coupons:'.md5($orderIdString);
        $cacheTags = ['order', 'coupon'];
        foreach ($orders as $order) {
            $cacheTags[] = 'order_'.$order->id;
            $cacheTags[] = 'order_'.$order->id.'_coupon';
        }
        $orderCoupons = Cache::tags($cacheTags)->remember($key, config('constants.CACHE_60'),
            function () use ($orders) {
                return $orders->getCoupons();
            });
        $this->generateSeoMetaTags(new SeoDummyTags('سفارش های من',
            'اطلاعات سفارش های شما', $request->url(),
            $request->url(), route('image', [
                'category' => '11',
                'w' => '100',
                'h' => '100',
                'filename' => $this->setting->site->siteLogo,
            ]), '100', '100', null));

        return response()->json([
            'orders' => $orders,
            'gateways' => $gateways,
            'transactions' => $transactions,
            'instalments' => $instalments,
            'orderCoupons' => $orderCoupons,
            'credit' => $credit
        ]);
    }

    public function partialUpdate(Request $request)
    {
        $user = $request->user();
        $this->fillUserFromRequest($request->all(), $user);

        $updateResult = false;
        if ($user->update()) {
            $updateResult = true;
        }

        if (!$updateResult) {
            return response()->json([
                'error' => [
                    'message' => 'خطا در اصلاح اطلاعات کاربر',
                ],
            ]);
        }

        $wallet = $user->wallets->where('wallettype_id', config('constants.WALLET_TYPE_GIFT'))->first();
        if (isset($wallet)) {
            $depositTransactions = $wallet->transactions->where('cost', '<', 0)->where('created_at', '>=',
                '2019-11-03 00:00:00');
            if ($depositTransactions->isNotEmpty()) {
                return response()->json([
                    'error' => [
                        'message' => 'شما قبلا هدیه کیف پول را دریافت کرده اید',
                    ]
                ]);
            }
        }

        $userCompletion = $user->completion('custom', LandingPageController::ROOZE_DANESH_AMOOZ_USER_NECESSARY_INFO);
        if ($userCompletion == 100) {
            $depositResult = $user->deposit(LandingPageController::ROOZE_DANESH_AMOOZ_GIFT_CREDIT,
                config('constants.WALLET_TYPE_GIFT'));
            if ($depositResult['result']) {
                return response()->json([
                    'message' => '14 هزار تومان اعتبار هدیه به کیف پول شما افزوده شد'
                ]);
            } else {
                return response()->json([
                    'error' => [
                        'message' => 'خطایی در اعدای هدیه کیف پول رخ داد. لطفا دوباره اقدام کنید',
                    ]
                ]);
            }
        }

        return response()->json([
            'error' => [
                'message' => 'اطلاعات شما تکمیل نیست',
            ],
        ]);
    }

    public function smsIndex(Request $request, User $user)
    {
        return SmsUser::with(['user', 'sms'])
            ->whereHas('sms', function ($q) use ($user) {
                $q->where('from_user_id', $user->id);
            })
            ->orWhere('user_id', $user->id)
            ->get();
    }

    public function couponOrder(Request $request)
    {
        $user = $request->user();
        $couponProduct = Product::find(Product::COUPON_PRODUCT);

        $couponOrder = OrderRepo::findCouponOrder($user->id, $couponProduct->id);

        $cost = $couponProduct->price;
        $cost = $cost['final'];
        if (isset($couponOrder)) {
            return response()->json([
                'message' => 'Coupon order found',
                'data' => $couponOrder
            ]);
        }
        $couponOrder = OrderRepo::createBasicCompletedOrder($user->id, config('constants.PAYMENT_STATUS_UNPAID'),
            $cost);
        if (isset($couponOrder)) {
            OrderproductRepo::createBasicOrderproduct($couponOrder->id, Product::COUPON_PRODUCT, $cost, $cost);
        }

        return response()->json([
            'message' => 'Coupon order created successfully',
            'data' => $couponOrder
        ]);
    }

    public function redirectToProfile(Request $request, $data)
    {
        $decryptedData = (array) decrypt($data);

        $userId = Arr::get($decryptedData, 'user_id');

        $user = $this->getUser($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 400);
        }

        Auth::login($user);
        event(new Authenticated($user));

        $redirectUrl = route('web.user.profile');

        $data = [
            'message' => 'Redirecting to user profile',
            'redirectUrl' => $redirectUrl,
        ];

        return response()->json($data, 200);
    }
}
