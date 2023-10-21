<?php

namespace App\Models;

use App\Classes\Taggable;
use App\Classes\Uploader\Uploader;
use App\Classes\Verification\MustVerifyMobileNumber;
use App\Collection\ProductCollection;
use App\Collection\UserCollection;
use App\Repositories\SmsBlackListRepository;
use App\Repositories\SubscriptionRepo;
use App\Traits\APIRequestCommon;
use App\Traits\CharacterCommon;
use App\Traits\DateTrait;
use App\Traits\HasWallet;
use App\Traits\Helper;
use App\Traits\MinioPhotoHandler;
use App\Traits\MustVerifyMobileNumberTrait;
use App\Traits\OrderCommon;
use App\Traits\User\{BonTrait,
    DashboardTrait,
    EmployeeTrait,
    FCMTrait,
    LotteryTrait,
    MutatorTrait,
    PaymentTrait,
    ProfileTrait,
    TaggableUserTrait,
    TeacherTrait,
    TrackTrait,
    VouchersTrait};
use App\Traits\Yalda1400;
use Carbon\Carbon;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\HasApiTokens;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, Taggable, MustVerifyMobileNumber, MustVerifyEmail
{

    use HasFactory;
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use \Illuminate\Auth\MustVerifyEmail;
    use Yalda1400;
    use HasApiTokens;
    use MustVerifyMobileNumberTrait;
    use Helper;
    use DateTrait;
    use SoftDeletes;
    use CascadeSoftDeletes;
    use HasWallet;
    use Notifiable;
    use APIRequestCommon;
    use CharacterCommon;
    use OrderCommon;

    use BonTrait;
    use DashboardTrait;
    use FCMTrait;
    use LotteryTrait;
    use MutatorTrait;
    use PaymentTrait;
    use ProfileTrait;
    use TaggableUserTrait;
    use TeacherTrait;
    use TrackTrait;
    use VouchersTrait;

    use EmployeeTrait;

    use MinioPhotoHandler;

    public const PHOTO_FIELD = 'photo';
    public const UPDATE_USER_PROVINCE_CITY_INDEX_PAGE_NAME = 'userProfileUpdateProvinceCityPage';

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */
    public const INDEX_PAGE_NAME = 'userPage';
    private const BE_PROTECTED = [
        'roles',
    ];
    public string $disk;
    protected $appends = [
        'info',
        'full_name',
        'userstatus',
        'roles',
//        'totalBonNumber',
        'jalaliCreatedAt',
        'jalaliUpdatedAt',
        'editLink',
        'removeLink',
        'cacheClearUrl',
        'logoutUserLink',
        'fatherMobile',
        'motherMobile',
    ];
    protected $cascadeDeletes = [
        'orders',
        'userbons',
        'useruploads',
        'bankaccounts',
        'contacts',
        'mbtianswers',
        //        'favorables',
    ];

    //columns being used for locking user's profile
    /**      * The attributes that should be mutated to dates.        */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'birthdate',
        'email_verified_at',
    ];
    protected $lockProfileColumns = [
        'shahr_id',
        'address',
        'postalCode',
        'school',
        'gender_id',
        'major_id',
        'email',
    ];
    protected $completeInfoColumns = [
        'photo',
        'shahr_id',
        'address',
        'postalCode',
        'school',
        'gender_id',
        'major_id',
        'grade_id',
        'phone',
        'bloodtype_id',
        'allergy',
        'medicalCondition',
        'diet',
    ];
    protected $medicalInfoColumns = [
        'bloodtype_id',
        'allergy',
        'medicalCondition',
        'diet',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile',
        'address',
        'postalCode',
        'school',
        'school_id',
        'educationalBase_id',
        'photo',
        'major_id',
        'grade_id',
        'birthdate',
        'gender_id',
        'email',
        'bio',
        'introducedBy',
        'phone',
        'whatsapp',
        'skype',
        'bloodtype_id',
        'allergy',
        'medicalCondition',
        'diet',
        'firstName',
        'lastName',
        'nationalCode',
        'nameSlug',
        'mobile',
        'userstatus_id',
        'techCode',
        'mobile_verified_code',
        'mobile_verified_at',
        'password', //For registering user
        'lockProfile',
        'shahr_id',
        'kartemeli',
        'inserted_by',
    ];
    protected $fillableByPublic = [
        'address',
        'postalCode',
        'school',
        'major_id',
        'grade_id',
        'birthdate',
        'gender_id',
        'email',
        'bio',
        'introducedBy',
        'phone',
        'whatsapp',
        'skype',
        'bloodtype_id',
        'allergy',
        'medicalCondition',
        'diet',
        'shahr_id',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'major',
        'major_id',
        'grade',
        'grade_id',
        'gender',
        'gender_id',
        'mobile_verified_code',
        'phone',
        'userstatus_id',
        'birthdate',
        'passwordRegenerated_at',
        'deleted_at',
        'techCode',
        'password',
        'remember_token',
        'wallets',
        'userbons',
    ];
    protected $hasHalfPriceService_cache;
    protected $cachedMethods = [
        'getHasHalfPriceServiceAttribute',
    ];

    public static function getNullInstant($visibleArray = [])
    {
        $user = new User();
        foreach ($visibleArray as $key) {
            $user->$key = null;
        }
        return $user;
    }

    public static function roleFilter($users, $rolesId)
    {
        $users = $users->whereHas('roles', function ($q) use ($rolesId) {
            $q->whereIn('id', $rolesId);
        });
        return $users;
    }

    public static function majorFilter($users, $majorsId)
    {

        if (in_array(0, $majorsId)) {
            $users = $users->whereDoesntHave('major');
        } else {
            $users = $users->whereIn('major_id', $majorsId);
        }

        return $users;
    }

    public function getAppToken()
    {
        $tokenResult = $this->createToken('Alaa App.');

        return [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'token_expires_at' => Carbon::parse($tokenResult->token->expires_at)
                ->toDateTimeString(),
        ];
    }

    public function get3AToken()
    {
        $tokenResult = $this->createToken('3A.');

        return [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'token_expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ];
    }

    public function routeNotificationForPhoneNumber()
    {
        return ltrim($this->mobile, '0');
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     *
     * @return UserCollection
     */
    public function newCollection(array $models = [])
    {
        return new UserCollection($models);
    }

    public function hasFavoredProduct(Product $product): bool
    {
        return $this->favoredProducts->where('id', $product->id)->isNotEmpty();
    }

    public function hasFavoredSet(Contentset $contentSet): bool
    {
        return $this->favoredSets->where('id', $contentSet->id)->isNotEmpty();
    }

    public function hasFavoredContent(Content $content): bool
    {
//        dd($this->getActiveFavoredContents());
        return $this->getActiveFavoredContents()->where('id', $content->id)->isNotEmpty();
    }

    public function hasFavoredTimepoint(Timepoint $timepoint): bool
    {
        return $this->favoredTimepoints->where('id', $timepoint->id)->isNotEmpty();
    }

    public function isEliteDeveloper(): bool
    {
        return in_array($this->id, [27244, 219548, 1961, 925019]);
    }


    /*
    |--------------------------------------------------------------------------
    | scope methods
    |--------------------------------------------------------------------------
    */

    public function isDeveloper(): bool
    {
        return $this->hasRole(config('constants.ROLE_DEVELOPER'));
    }

    public function hasPurchasedAnything(): bool
    {
        $key = 'user:hasPurchasedAnything:'.$this->cacheKey();
        return Cache::tags(['userAsset_'.$this->id,])->remember($key, config('constants.CACHE_600'), function () {
            return $this->orders->whereIn('orderstatus_id', [
                config('constants.ORDER_STATUS_CLOSED'), config('constants.ORDER_STATUS_POSTED'),
                config('constants.ORDER_STATUS_REFUNDED'), config('constants.ORDER_STATUS_READY_TO_POST'),
                config('constants.ORDER_STATUS_PENDING'), config('constants.ORDER_STATUS_BLOCKED'),
            ])
                ->where('paymentstatus_id', '<>', config('constants.PAYMENT_STATUS_UNPAID'))
                ->isNotEmpty();

        });
    }

    public function cacheKey()
    {
        $key = $this->getKey();
        $time = (optional($this->updated_at)->timestamp ?: optional($this->created_at)->timestamp) ?: 0;

        return sprintf('%s:%s-%s', $this->getTable(), $key, $time);
    }

    /**
     * @param  Builder  $query
     * @param  array  $roles
     *
     * @return mixed
     */
    public function scopeRole($query, array $roles)
    {
        return $query->whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('id', $roles);
        });
    }

    /**
     * @param  Builder  $query
     *
     * @param  string  $roleName
     *
     * @return mixed
     */
    public function scopeRoleName($query, string $roleName)
    {
        $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | relations
    |--------------------------------------------------------------------------
    */

    /**
     * @param  Builder  $query
     *
     *
     * @param  string  $permissionName
     *
     * @return mixed
     */
    public function scopePermissionName($query, string $permissionName)
    {
        $query->whereHas('permissions', function ($q) use ($permissionName) {
            $q->where('name', $permissionName);
        });
    }

    /**
     * @param  Builder  $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('userstatus_id', config('constants.USER_STATUS_ACTIVE'));
    }

    public function scopeId($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }

    public function favorableLists()
    {
        return $this->hasMany(FavorableList::class);
    }

    public function consultants()
    {
        return $this->belongsToMany(Consultant::class);
    }

    public function entekhabReshte()
    {
        return $this->hasOne(EntekhabReshte::class);
    }

    public function ewanoUser()
    {
        return $this->hasOne(EwanoUser::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class);
    }

    public function liveConductors()
    {
        return $this->belongsToMany(
            Conductor::class,
            'live_conductor_user',
            'user_id',
            'live_conductor_id'
        )->withTimestamps();
    }

    public function studyEventReports()
    {
        return $this->hasMany(StudyEventReport::class);
    }

    public function websiteSetting()
    {
        return $this->hasOne(Websitesetting::class);
    }

    public function consultant()
    {
        return $this->hasOne(BonyadEhsanConsultant::class);
    }

    public function useruploads()
    {
        return $this->hasMany(Userupload::class);
    }

    public function mbtianswers()
    {
        return $this->hasMany(Mbtianswer::class);
    }

    public function usersurveyanswers()
    {
        return $this->hasMany(Usersurveyanswer::class);
    }

    public function eventresults()
    {
        return $this->hasMany(Eventresult::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::Class);
    }

    public function firebasetokens()
    {
        return $this->hasMany(Firebasetoken::class);
    }

    //ToDo : to be removed
//    public function subscriptions()
//    {
//        return $this->hasMany(Subscription::Class);
//    }

    //TODO : Please remove this. I checked it and I think it hasn't been used anywhere.
//    public function smses()
//    {
//        return $this->hasMany(SMS::Class , 'user_id' , 'id');
//    }

    public function lotteries()
    {
        return $this->belongsToMany(Lottery::Class)
            ->withPivot('rank', 'prizes');
    }

    public function sms()
    {
        return $this->hasMany(SmsUser::Class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketMessages()
    {
        return $this->hasMany(TicketMessage::Class, 'user_id', 'id');
    }

    public function ticketLogs()
    {
        return $this->hasMany(TicketActionLog::Class, 'user_id', 'id');
    }

    public function subscribedEvents()
    {
        return $this->morphedByMany(Event::class, 'subscription')->withTimestamps();
    }

    public function getActiveStudyEvents()
    {
        return $this->studyEvents()->latest('pivot_created_at');
    }

    public function studyEvents()
    {
        return $this->belongsToMany(Studyevent::class)->withTimestamps();
    }

    public function validSubscribedAttributes()
    {
        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran'));

        return $this->subscribedProducts()
            ->where(function ($q2) {
                $q2->whereNull('usage_limit')->orWhereRaw('subscriptions.usage_number < subscriptions.usage_limit');
            })
            ->where(function ($q) use ($now) {
                $q->where('valid_since', '<', $now)
                    ->orWhereNull('valid_since');
            })
            ->where(function ($q) use ($now) {
                $q->where('valid_until', '>', $now)
                    ->orWhereNull('valid_until');
            });
    }

    public function subscribedProducts()
    {
        return $this->morphedByMany(Product::class, 'subscription');
    }

    /**
     * @param $products
     *
     * @return mixed
     */
    public function getOrdersThatHaveSpecificProduct(ProductCollection $products)
    {
        $validOrders = $this->orders()
            ->whereHas('orderproducts', function ($q) use ($products) {
                $q->whereIn('product_id', $products->pluck('id'));
            })
            ->whereIn('orderstatus_id', [
                config('constants.ORDER_STATUS_CLOSED'),
                config('constants.ORDER_STATUS_POSTED'),
                config('constants.ORDER_STATUS_READY_TO_POST'),
            ])
            ->whereIn('paymentstatus_id', [
                config('constants.PAYMENT_STATUS_PAID'),
            ])
            ->get();
        return $validOrders;
    }

    public function getPurchasedOrderproduct(int $productId): ?Orderproduct
    {
        return $this->orderproducts()
            ->whereHas('order', function ($q) {
                $q->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                    ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'));
            })
            ->where('product_id', $productId)
            ->first();
    }

    public function getOrderedOrderproduct(array $productId): ?Orderproduct
    {
        return $this->orderproducts()
            ->whereHas('order', function ($q) {
                $q->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                    ->whereIn('paymentstatus_id', [
                        config('constants.PAYMENT_STATUS_PAID'),
                        config('constants.PAYMENT_STATUS_INDEBTED'),
                        config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
                        config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
                    ]);
            })
            ->whereIn('product_id', $productId)
            ->first();
    }

    public function countPurchasedProducts(array $products, string $since = null, string $till = null): int
    {
        return $this->orderproducts()
            ->where('orderproducttype_id', '<>', config('constants.ORDER_PRODUCT_GIFT'))
            ->whereHas('order', function ($q) use ($since, $till) {
                $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                    ->whereIn('paymentstatus_id', [
                        config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                    ]);

                if (isset($since)) {
                    $q->where('completed_at', '>=', $since);
                }

                if (isset($till)) {
                    $q->where('completed_at', '<=', $till);
                }
            })->whereIn('product_id', $products)->get()->count();
    }

    public function getTicketRolesAttribute()
    {
        $key = 'user:ticketRoles:'.$this->cacheKey();
        return Cache::tags(['user', 'user_'.$this->id, 'user_'.$this->id.'_ticketRoles'])->remember($key,
            config('constants.CACHE_600'), function () {
                $roles = $this->roles()->where('team_id', Team::SUPPORT_TEAM_ID)->get();
                if ($roles->isEmpty()) {
                    return null;
                }
                return $roles->first()->display_name;
            });
    }

    public function hasOrder(int $orderId): bool
    {
        return $this->orders()->where('id', $orderId)->get()->isNotEmpty();
    }

    /**
     * Get userstatus that belongs to
     *
     * @return BelongsTo
     */
    public function userstatus()
    {
        return $this->belongsTo(Userstatus::class);
    }

    /**
     * Get related help categories
     *
     * @return BelongsToMany
     */
    public function helpCategories()
    {
        return $this->belongsToMany(Category::class, 'help_categories_users', 'user_id', 'category_id');
    }

    public function getTicketRoleTitle()
    {
        if (!$this->hasPermission(config('constants.ANSWER_TICKET'))) {
            return 'کاربر';
        }

        $roleName = $this->ticket_roles;

        if (isset($roleName)) {
            return $roleName;
        }

        return 'پشتیبان';
    }

    public function getProvincesAttribute()
    {
        return Ostan::all();
    }

    public function shahr()
    {
        return $this->belongsTo(Shahr::class, 'shahr_id');
    }

    /**
     * Get the referral code for the user.
     */
    public function refferalCode()
    {
        return $this->hasOne(ReferralCode::class, 'owner_id');
    }

    public function usedReferralCode()
    {
        return $this->hasMany(ReferralCodeUser::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }

    public function batchContentInserts()
    {
        return $this->hasMany(BatchContentInsert::class);
    }

    /**
     * Return any user who has at least one role on the site.
     *
     * @return User|Builder
     */
    public function scopeAnyRole()
    {
        return $this->whereHas('roles');
    }

    /**
     * Check that the user has at least one role on the site.
     *
     * @return bool
     */
    public function hasAnyRole(): bool
    {
        return $this->roles()->get()->isNotEmpty();
    }

    /**
     * @return string
     */
    public function getFullNameWithIdAttribute(): string
    {
        return "#{$this->id} {$this->full_name}";
    }

    public function scopeEmployee($query)
    {
        return $query->roleName(config('constants.ROLE_EMPLOYEE'));
    }

    public function scopeWhereNotInBlackList($query)
    {
        return $query->whereNotIn('mobile',
            SmsBlackListRepository::getBlockedList()?->get()?->pluck('mobile')?->toArray());
    }

    /**
     * @return BelongsToMany
     */
    public function watchContentSets(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'watch_histories', 'user_id', 'watchable_id')
            ->where('watchable_type', config('constants.MORPH_MAP_MODELS.set.model'));
    }

    /**
     * @return BelongsToMany
     */
    public function watchProducts(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'watch_histories', 'user_id', 'watchable_id')
            ->where('watchable_type', config('constants.MORPH_MAP_MODELS.product.model'));
    }

    /**
     * @param  int  $contentId
     *
     * @return bool
     */
    public function hasWatched(int $contentId): bool
    {
        return $this->watchContents()->where('watchable_id', $contentId)->exists();
    }

    /**
     * @return BelongsToMany
     */
    public function watchContents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'watch_histories', 'user_id', 'watchable_id')
            ->where('watchable_type', config('constants.MORPH_MAP_MODELS.content.model'));
    }

    public function ownsReferralCode(): bool
    {
        return $this->referralRequests()->with('referralCodes')->get()->pluck('referralCodes')->flatten()->count() != 0;
    }

    public function referralRequests()
    {
        return $this->hasMany(ReferralRequest::class, 'owner_id');
    }

    public function getImageAttribute()
    {
        return $this->getRawOriginal('photo');
    }

    public function setDisk()
    {
        return $this->disk = config('disks.PROFILE_IMAGE_MINIO');
    }

    public function getHasHalfPriceServiceAttribute()
    {
        if (!is_null($this->hasHalfPriceService_cache)) {
            return $this->hasHalfPriceService_cache === false ? null : $this->hasHalfPriceService_cache;
        }
        $this->hasHalfPriceService_cache =
            SubscriptionRepo::validProductSubscriptionOfUser($this->id, Product::TIMEPOINT_SUBSCRIPTON_PRODUCTS);
        if ($this->hasHalfPriceService_cache === null) {
            $this->hasHalfPriceService_cache = false;
        }
        return $this->hasHalfPriceService_cache === false ? null : $this->hasHalfPriceService_cache;
    }

    public function voipOperator()
    {
        return $this->hasone(VoipOperator::class, 'operator_id', 'id');
    }

    public function voipCalls()
    {
        return $this->hasMany(Voip::class, 'caller_id', 'id');
    }

    public function filterOrdersByProductsOfContent(Content $content)
    {
        return Cache::tags(['user_orders_'.$content->id, 'userAsset_'.$this->id])
            ->remember("'user_orders:user-'.$this->id.':content-'.$content->id", config('constants.CACHE_600'),
                function () use ($content) {
                    return $this->orders()
                        ->whereIn('orderstatus_id', [config('constants.ORDER_STATUS_CLOSED')])
                        ->whereIn('paymentstatus_id', array_merge([config('constants.PAYMENT_STATUS_INDEBTED')],
                            Order::getDoneOrderPaymentStatus()))
                        ->whereHas('orderproducts', fn($q) => $q->whereIn('product_id', $content->productsIdArray()))
                        ->get();
                });

    }

    /**
     * @param  Orderproduct  $orderproduct
     *
     * @return void
     */
    public function unUsedSubscription(Orderproduct $orderproduct): void
    {
        $user = $this;
        $userDiscountSubscription =
            isset($user) ? SubscriptionRepo::validProductSubscriptionOfUser($user->id,
                [Product::SUBSCRIPTION_12_MONTH]) : null;
        if (!isset($userDiscountSubscription)) {
            return;
        }
        $subscriptionOrderproductIdArray =
            optional(optional($userDiscountSubscription->values)->discount)->orderproduct_id;
        if (isset($subscriptionOrderproductIdArray) && !empty($subscriptionOrderproductIdArray) && in_array($orderproduct->id,
                $subscriptionOrderproductIdArray)) {
            $currentUsage = optional(optional(optional($userDiscountSubscription)->values)->discount)->usage_limit;
            $userDiscountSubscription->setUsageLimit(min($currentUsage + 1, 1));
            $userDiscountSubscription->unsetOrderproductId();
            $userDiscountSubscription->updateWithoutTimestamp();
        }

    }

    public function getMaximumActiveCoupon(): ?Coupon
    {
        $key = 'getMaximumActiveCoupon:'.$this->cacheKey();
        $tags = ['user', 'user_'.$this->id, 'coupon_user_'.$this->id];
        $user = $this;
        return Cache::tags($tags)->remember($key, config('constants.CACHE_600'), function () use ($user) {
            return Coupon::enable()->valid()->usageLeft()->orderBy('discount', 'desc')
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('id', $user->id);
                })->take(1)->get()->first();
        });
    }

    public function getPermissionsThroughRoles()
    {
        $roles = $this->roles()->get() ?? [];
        $permissions = collect();
        foreach ($roles as $role) {
            $permissions = $permissions->merge($role->permissions()->get());
        }

        return $permissions;
    }

    public function bankaccounts()
    {
        return $this->hasMany(Bankaccount::class);
    }

    public function commissions()
    {
        return $this->hasMany(UserCommission::class);
    }

    public function salesManProfile()
    {
        return $this->hasOne(SalesManProfile::class);
    }

    public function getKartemeliAttribute($value)
    {
        if ($value) {
            return Uploader::privateUrl(config('disks.KARTE_MELI_IMAGE_MINIO'), 720, null, $value);
        }
        return null;
    }

    public function _3aExamResult()
    {
        return $this->hasMany(_3a_exam_result::class);
    }

    public function getFatherMobileAttribute()
    {
        return $this->relativeNormalMobiles(config('constants.FATHER_RELATIVE_ID'))?->first()?->phoneNumber;
    }

    public function relativeNormalMobiles($relativeType)
    {
        return $this->contacts()
            ?->where('relative_id', $relativeType)
            ?->first()
            ?->phones()
            ?->whereNotInBlackList()
            ?->where('phonetype_id', 1)
            ?->get()
            ?->sortBy('priority') ?? collect();
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function getMotherMobileAttribute()
    {
        return $this->relativeNormalMobiles(config('constants.MOTHER_RELATIVE_ID'))?->first()?->phoneNumber;
    }

    public function scopeInsertedByIds(Builder $query, $inserted_by_ids)
    {
        return $query->whereIn('inserted_by', $inserted_by_ids);
    }

    public function scopeBonyadUser(Builder $query)
    {
        return $query->pluck('id');
    }

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'inserted_by', 'id');
    }

    public function parents()
    {
        return $this->belongsToMany(User::class, 'bonyad_parents', 'user_id', 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany(User::class, 'bonyad_parents', 'parent_id', 'user_id');
    }

}
