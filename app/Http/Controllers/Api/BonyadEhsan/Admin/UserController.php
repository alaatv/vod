<?php

namespace App\Http\Controllers\Api\BonyadEhsan\Admin;

use App\Classes\BonyadUserPolicy;
use App\Classes\CacheFlush;
use App\Events\BonyadEhsanUserRegistered;
use App\Events\BonyadEhsanUserUpdate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EditUserApiRequest;
use App\Http\Requests\BonyadEhsan\Admin\CreateGroupUserRequest;
use App\Http\Requests\BonyadEhsan\Admin\CreateMoshaverBonyadRequest;
use App\Http\Requests\BonyadEhsan\Admin\CreateNetworkBonyadRequest;
use App\Http\Requests\BonyadEhsan\Admin\CreateUserRequest;
use App\Http\Requests\BonyadEhsan\Admin\EditUserRequest;
use App\Http\Requests\BonyadEhsan\Admin\studentLimitRequest;
use App\Http\Resources\UserForBonyadEhsan;
use App\Models\BonyadEhsanConsultant;
use App\Models\Contact;
use App\Models\Orderproduct;
use App\Models\Phone;
use App\Models\User;
use App\Repositories\Loging\ActivityLogRepo;
use App\Repositories\UserRepo;
use App\Services\BonyadService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_PANEL_ACCESS'))->only(['showLoginUser']);
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_INSERT_USER'))->only(['store']);
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_INSERT_MOSHAVER'))->only(['storeMoshaver']);
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_INSERT_NETWORK'))->only(['storeNetwork']);
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_INSERT_SUB_NETWORK'))->only(['storeSubNetwork']);
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_UPDATE_USER'))->only(['update']);
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_UPDATE_STUDENT_LIMIT'))->only(['studentLimit']);
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_DELETE_USERS'))->only(['delete']);
    }

    public function showLoginUser(Request $request)
    {
        return response()->json(
            [
                'data' => new UserForBonyadEhsan($request->user()),
            ]
        );
    }

    public function show(User $user)
    {
        BonyadUserPolicy::check($user);
        return response()->json([
            'data' => new UserForBonyadEhsan($user)
        ]);
    }

    public function store(CreateUserRequest $request)
    {
        return myAbort(Response::HTTP_LOCKED, 'با توجه به اتمام مهلت سرویس شما، شما نمی توانید کاربر جدید اضافه کنید');
        Gate::allowIf(fn($user) => $user->consultant->exists());

        $consultant = $request->user()?->consultant;
        if ((int) ($consultant?->student_register_number) >= (int) ($consultant?->student_register_limit)) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'شما حداکثر کاربر ممکن را وارد کرده اید.');
        }


        $validatedData = $request->validated();
        $user = UserRepo::find($request->get('mobile'), $request->get('nationalCode'))->first();

        if (!isset($user)) {
            $validatedData = array_merge($validatedData, [
                'password' => bcrypt($request->get('nationalCode')),
                'userstatus_id' => config('constants.USERBON_STATUS_ACTIVE'),
                'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                'mobile_verified_at' => now('Asia/Tehran'),
                'inserted_by' => $request->user()->id
            ]);

            $user = User::create($validatedData);
            ActivityLogRepo::logBonyadEhsanUserRegistration($request->user(), $user);
        } else {
            $user->update([
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'shahr_id' => $validatedData['shahr_id'],
                'gender_id' => $validatedData['gender_id'],
                'major_id' => $validatedData['major_id'],
                'address' => $validatedData['address'],
                'phone' => $validatedData['phone'],
                'inserted_by' => $request->user()->id
            ]);
        }
        $user->parents()->sync(UserRepo::parentUsersForSync($request->user()->id));
        $userHaveBonyadRole = $user->roles()->pluck('name')->intersect(array_keys(BonyadService::getRoles()));
        if ($userHaveBonyadRole->isEmpty()) {
            event(new BonyadEhsanUserRegistered($user));
            $this->insertParentContact($user, $request->get('father_mobile'), 'پدر');
            $this->insertParentContact($user, $request->get('mother_mobile'), 'مادر');

            CacheFlush::flushAssetCache($user);
        } else {
            return myAbort(Response::HTTP_BAD_REQUEST, 'این کاربر قبلا با گروه بندی دیگری ثبت شده است. ');
        }


        return response()->json([
            'data' => [
                'id' => $user->id,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditUserApiRequest  $request
     * @param  User  $user
     *
     * @return UserForBonyadEhsan
     */
    public function update(EditUserRequest $request, User $user)
    {
        $validatedData = $request->validated();
        $requestNationalCode = $request->get('nationalCode');
        if ($user->nationalCode != $requestNationalCode) {
            $validatedData = array_merge($validatedData, ['password' => bcrypt($requestNationalCode)]);
        }
        $user->update($validatedData);
        if ($user->roles()->pluck('name')->contains(config('constants.ROLE_BONYAD_EHSAN_USER'))) {
            //fatherMobile
            $contact = $user->contacts()->updateOrCreate([
                'user_id' => $user->id, 'relative_id' => config('constants.FATHER_RELATIVE_ID')
            ], [
                'name' => 'پدر',
                'contacttype_id' => 1,
                'relative_id' => config('constants.FATHER_RELATIVE_ID')
            ]);
            $contact->phones()->updateOrCreate(['contact_id' => $contact->id], [
                'phonetype_id' => 1,
                'phoneNumber' => $request->get('fatherMobile')
            ]);

            //motherMobile
            $contact = $user->contacts()->updateOrCreate([
                'user_id' => $user->id, 'relative_id' => config('constants.MOTHER_RELATIVE_ID')
            ], [
                'name' => 'مادر',
                'contacttype_id' => 1,
                'relative_id' => config('constants.MOTHER_RELATIVE_ID')
            ]);
            $contact->phones()->updateOrCreate(['contact_id' => $contact->id], [
                'phonetype_id' => 1,
                'phoneNumber' => $request->get('motherMobile')
            ]);

            event(new BonyadEhsanUserUpdate($user));
        }

        CacheFlush::flushAssetCache($user);
        Cache::tags(['user_'.$user->id])->flush();


        return new UserForBonyadEhsan($user);
    }

    /**
     * @param        $user
     * @param  string  $parentMobileNumber
     * @param  string  $parentName
     */
    private function insertParentContact($user, string $parentMobileNumber, string $parentName)
    {
        $relativeId = $parentName == 'پدر' ? 1 : 2;

        $fatherContact = $user->contacts->where('relative_id', $relativeId)->first();
        if (!isset($fatherContact)) {
            $fatherContact = Contact::create([
                'name' => $parentName,
                'user_id' => $user->id,
                'contacttype_id' => 1,
                'relative_id' => $relativeId,
            ]);
        }

        $fatherMobile = $fatherContact->phones->first();
        if (!isset($fatherMobile)) {
            return Phone::create([
                'phoneNumber' => $parentMobileNumber,
                'contact_id' => $fatherContact->id,
                'phonetype_id' => 1,
            ]);
        }

        return $fatherMobile->update([
            'phoneNumber' => $parentMobileNumber,
        ]);
    }

    public function storeMoshaver(CreateMoshaverBonyadRequest $request)
    {
        return myAbort(Response::HTTP_LOCKED, 'با توجه به اتمام مهلت سرویس شما، شما نمی توانید کاربر جدید اضافه کنید');
        $validatedData = $request->validated();

        if ($validatedData['student_register_limit'] > ($request->user()->consultant->student_register_limit - $request->user()->consultant->student_register_number)) {
            return response()->json([
                'data' => [
                    'error' => 'تعداد کاربر وارد شده بالاتر از سهمیه افزودن کاربر شما است.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
        $user = UserRepo::find($request->get('mobile'), $request->get('nationalCode'))->first();

        if (!isset($user)) {
            $validatedData = array_merge($validatedData, [
                'password' => bcrypt($request->get('nationalCode')),
                'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                'mobile_verified_at' => now('Asia/Tehran'),
                'inserted_by' => $request->user()->id
            ]);

            $user = User::create($validatedData);
            ActivityLogRepo::logBonyadEhsanUserRegistration($request->user(), $user);
        } else {
            $user->update([
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'shahr_id' => $validatedData['shahr_id'],
                'gender_id' => $validatedData['gender_id'],
                'inserted_by' => $request->user()->id
            ]);
        }
        $user->parents()->sync(UserRepo::parentUsersForSync($request->user()->id));
        $userHaveBonyadRole = $user->roles()->pluck('name')->intersect(array_keys(BonyadService::getRoles()));
        if ($userHaveBonyadRole->isEmpty()) {
            $request->user()->consultant->increaseRegistrationNumber($request->get('student_register_limit'));
            $user->consultant()->updateOrCreate(
                ['user_id' => $user->id],
                ['student_register_limit' => $request->get('student_register_limit'), 'student_register_number' => 0]
            );
            $user->attachRole(config('constants.ROLE_BONYAD_EHSAN_MOSHAVER'));
        } else {
            return myAbort(Response::HTTP_BAD_REQUEST, 'این کاربر قبلا با گروه بندی دیگری ثبت شده است. ');
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
            ],
        ], Response::HTTP_OK);
    }

    public function storeNetwork(CreateNetworkBonyadRequest $request)
    {
        return myAbort(Response::HTTP_LOCKED, 'با توجه به اتمام مهلت سرویس شما، شما نمی توانید کاربر جدید اضافه کنید');
        $validatedData = $request->validated();

        if ($validatedData['student_register_limit'] > ($request->user()->consultant->student_register_limit - $request->user()->consultant->student_register_number)) {
            return response()->json([
                'data' => [
                    'error' => 'تعداد کاربر وارد شده بالاتر از سهمیه افزودن کاربر شما است.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
        $user = UserRepo::find($request->get('mobile'), $request->get('nationalCode'))->first();
        if (!isset($user)) {
            $validatedData = array_merge($validatedData, [
                'password' => bcrypt($request->get('nationalCode')),
                'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                'mobile_verified_at' => now('Asia/Tehran'),
                'inserted_by' => $request->user()->id
            ]);

            $user = User::create($validatedData);
            ActivityLogRepo::logBonyadEhsanUserRegistration($request->user(), $user);
        } else {
            $user->update([
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'shahr_id' => $validatedData['shahr_id'],
                'gender_id' => $validatedData['gender_id'],
                'inserted_by' => $request->user()->id
            ]);
        }
        $user->parents()->sync(UserRepo::parentUsersForSync($request->user()->id));
        $userHaveBonyadRole = $user->roles()->pluck('name')->intersect(array_keys(BonyadService::getRoles()));
        if ($userHaveBonyadRole->isEmpty()) {
            $request->user()->consultant->increaseRegistrationNumber($request->get('student_register_limit'));
            $user->consultant()->updateOrCreate(
                ['user_id' => $user->id],
                ['student_register_limit' => $request->get('student_register_limit'), 'student_register_number' => 0]
            );
            $user->attachRole(config('constants.ROLE_BONYAD_EHSAN_NETWORK'));
        } else {
            return myAbort(Response::HTTP_BAD_REQUEST, 'این کاربر قبلا با گروه بندی دیگری ثبت شده است. ');
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
            ],
        ], Response::HTTP_OK);
    }

    public function storeSubNetwork(CreateNetworkBonyadRequest $request)
    {
        return myAbort(Response::HTTP_LOCKED, 'با توجه به اتمام مهلت سرویس شما، شما نمی توانید کاربر جدید اضافه کنید');
        $validatedData = $request->validated();

        if ($validatedData['student_register_limit'] > ($request->user()->consultant->student_register_limit - $request->user()->consultant->student_register_number)) {
            return response()->json([
                'data' => [
                    'error' => 'تعداد کاربر وارد شده بالاتر از سهمیه افزودن کاربر شما است.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
        $user = UserRepo::find($request->get('mobile'), $request->get('nationalCode'))->first();
        if (!isset($user)) {
            $validatedData = array_merge($validatedData, [
                'password' => bcrypt($request->get('nationalCode')),
                'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                'mobile_verified_at' => now('Asia/Tehran'),
                'inserted_by' => $request->user()->id
            ]);

            $user = User::create($validatedData);
            ActivityLogRepo::logBonyadEhsanUserRegistration($request->user(), $user);
        } else {
            $user->update([
                'firstName' => $validatedData['firstName'],
                'lastName' => $validatedData['lastName'],
                'shahr_id' => $validatedData['shahr_id'],
                'gender_id' => $validatedData['gender_id'],
                'inserted_by' => $request->user()->id
            ]);
        }
        $user->parents()->sync(UserRepo::parentUsersForSync($request->user()->id));
        $userHaveBonyadRole = $user->roles()->pluck('name')->intersect(array_keys(BonyadService::getRoles()));

        if ($userHaveBonyadRole->isEmpty()) {
            $request->user()->consultant->increaseRegistrationNumber($request->get('student_register_limit'));
            $user->consultant()->updateOrCreate(
                ['user_id' => $user->id],
                ['student_register_limit' => $request->get('student_register_limit'), 'student_register_number' => 0]
            );
            $user->attachRole(config('constants.ROLE_BONYAD_EHSAN_SUB_NETWORK'));
        } else {
            return myAbort(Response::HTTP_BAD_REQUEST, 'این کاربر قبلا با گروه بندی دیگری ثبت شده است. ');
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
            ],
        ], Response::HTTP_OK);
    }

    public function storeGroupUser(CreateGroupUserRequest $request)
    {
        return myAbort(Response::HTTP_LOCKED, 'با توجه به اتمام مهلت سرویس شما، شما نمی توانید کاربر جدید اضافه کنید');
        $validatedData = $request->validated();
        $authUser = auth('api')->user();
        if ($validatedData['type'] == 'student') {
            $registration = count($validatedData['users']);
        } else {
            $registration = array_sum(array_column($validatedData['users'], 'student_register_limit'));
        }
        if ($registration > ($authUser->consultant->student_register_limit - $authUser->consultant->student_register_number)) {
            return response()->json([
                'data' => [
                    'error' => 'تعداد کاربر وارد شده بالاتر از سهمیه افزودن کاربر شما است.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try {
            if ($validatedData['type'] == 'student') {
                foreach ($validatedData['users'] as $data) {
                    $user = UserRepo::find($data['mobile'], $data['nationalCode'])->first();
                    if (!isset($user)) {
                        $data = array_merge($data, [
                            'password' => bcrypt($data['nationalCode']),
                            'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                            'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                            'mobile_verified_at' => now('Asia/Tehran'),
                            'inserted_by' => $request->user()->id
                        ]);
                        $user = User::create($data);
                        ActivityLogRepo::logBonyadEhsanUserRegistration($request->user(), $user);

                    } else {
                        $user->update([
                            'firstName' => $data['firstName'],
                            'lastName' => $data['lastName'],
                            'shahr_id' => $data['shahr_id'],
                            'gender_id' => $data['gender_id'],
                            'major_id' => $data['major_id'],
                            'address' => $data['address'],
                            'phone' => $data['phone'],
                            'inserted_by' => $request->user()->id
                        ]);
                    }
                    $user->parents()->sync(UserRepo::parentUsersForSync($request->user()->id));
                    event(new BonyadEhsanUserRegistered($user));
                    $this->insertParentContact($user, $data['father_mobile'], 'پدر');
                    $this->insertParentContact($user, $data['mother_mobile'], 'مادر');
                    CacheFlush::flushAssetCache($user);
                }

            } else {
                foreach ($validatedData['users'] as $data) {
                    $user = UserRepo::find($data['mobile'], $data['nationalCode'])->first();
                    if (!isset($user)) {
                        $data = array_merge($data, [
                            'password' => bcrypt($data['nationalCode']),
                            'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                            'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                            'mobile_verified_at' => now('Asia/Tehran'),
                            'inserted_by' => $request->user()->id
                        ]);
                        $user = User::create($data);
                        ActivityLogRepo::logBonyadEhsanUserRegistration($request->user(), $user);

                    } else {
                        $user->update([
                            'firstName' => $data['firstName'],
                            'lastName' => $data['lastName'],
                            'shahr_id' => $data['shahr_id'],
                            'gender_id' => $data['gender_id'],
                            'inserted_by' => $request->user()->id
                        ]);
                    }
                    $user->parents()->sync(UserRepo::parentUsersForSync($request->user()->id));
                    $authUser->consultant->increaseRegistrationNumber($data['student_register_limit']);
                    $user->consultant()->updateOrCreate(
                        ['user_id' => $user->id],
                        ['student_register_limit' => $data['student_register_limit'], 'student_register_number' => 0]
                    );
                    $role = match ($validatedData['type']) {
                        'network' => config('constants.ROLE_BONYAD_EHSAN_NETWORK'),
                        'subnetwork' => config('constants.ROLE_BONYAD_EHSAN_SUB_NETWORK'),
                        'moshaver' => config('constants.ROLE_BONYAD_EHSAN_MOSHAVER'),
                    };
                    if (!$user->hasRole($role)) {
                        $user->attachRole($role);
                    }
                }
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'data' => [
                    'error' => $exception->getMessage()
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        return response()->json([
            'data' => [
                'message' => 'کاربران با موفقیت افزوده شدند.'
            ],
        ], Response::HTTP_OK);
    }

    public function delete(User $user)
    {
        BonyadUserPolicy::check($user);
        $rolesKey = collect(array_keys(BonyadService::getRoles()));
        $userRole = $rolesKey->intersect($user->roles()->pluck('name'))->first();
        if (empty($userRole)) {
            return response()->json([
                'data' => [
                    'message' => 'not found'
                ]
            ], 404);
        }
        try {
            DB::beginTransaction();
            $user->detachRole($userRole);
            $insertorConsultantData = BonyadEhsanConsultant::find($user->inserted_by);
            if ($userRole == config('constants.ROLE_BONYAD_EHSAN_USER')) {
                $insertorConsultantData->update([
                    'student_register_number' => ($insertorConsultantData->student_register_number) - 1
                ]);
                $removableOrders = $user->orders()
                    ->where('seller', '=', config('constants.ALAA_SELLER'))
                    ->where('orderstatus_id', '=', config('constants.ORDER_STATUS_CLOSED'))
                    ->where('paymentstatus_id', '=', config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'))
                    ->where('coupon_id', '=', config('constants.BONYAD_COUPON_ID'));
                Orderproduct::whereIn('order_id', $removableOrders->pluck('id'))->delete();
                $removableOrders->delete();

            } else {
                $insertorConsultantData->update([
                    'student_register_number' => ($insertorConsultantData->student_register_number) - $user->consultant->student_register_limit
                ]);
                $user->consultant?->forceDelete();
                $subUsers = User::whereHas('parents', function ($query) use ($user) {
                    return $query->where('id', $user->id);
                })->get();
                foreach ($subUsers as $subUser) {
                    $subUserRole = $rolesKey->intersect($subUser->roles()->pluck('name'))->first();
                    if ($subUserRole == config('constants.ROLE_BONYAD_EHSAN_USER')) {
                        $removableOrders = $subUser->orders()
                            ->where('seller', '=', config('constants.ALAA_SELLER'))
                            ->where('orderstatus_id', '=', config('constants.ORDER_STATUS_CLOSED'))
                            ->where('paymentstatus_id', '=', config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'))
                            ->where('coupon_id', '=', config('constants.BONYAD_COUPON_ID'));
                        Orderproduct::whereIn('order_id', $removableOrders->pluck('id'))->delete();
                        $removableOrders->delete();
                    }
                    $subUser->detachRole($subUserRole);
                    $subUser->consultant?->forceDelete();
                    $subUser->parents()->sync([]);
                    $subUser->update(['inserted_by' => null]);
                }
            }
            $user->parents()->sync([]);
            $user->update(['inserted_by' => null]);
            DB::commit();
            return response()->json([
                'data' => [
                    'message' => $user->id.' removed successfully'
                ]
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'data' => [
                    'error' => $exception->getMessage()
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function consultantInfo(BonyadEhsanConsultant $consultant)
    {
        Gate::allowIf(fn($user
        ) => $user->is($consultant->user) || $consultant->user->hasPermission(config('constants.BONYAD_EHSAN_CONSULTANT_SHOW')));

        return response([
            'data' => [
                'usage_limit' => $consultant->student_register_limit,
                'usage_number' => $consultant->student_register_number,
            ]
        ], Response::HTTP_OK);
    }

    public function studentLimit(studentLimitRequest $request)
    {
        $limit = $request->student_register_limit;
        $authUser = $request->user()->load('consultant');
        $targetUser = User::find($request->user_id)->load('consultant');
        $parentIds = UserRepo::parentUsers($targetUser->inserted_by);
        $parentIds['requestUser'] = [
            'id' => $authUser->id,
            'student_register_limit' => $authUser->consultant->student_register_limit,
            'student_register_number' => $authUser->consultant->student_register_number,
        ];
        $parentIds['targetUser'] = [
            'id' => $targetUser->id,
            'student_register_limit' => $targetUser->consultant->student_register_limit,
            'student_register_number' => $targetUser->consultant->student_register_number,
        ];


        if ($limit > $targetUser->consultant->student_register_limit) { //ezafe mishavad
            $registerLimit = $limit - $targetUser->consultant->student_register_limit;
            if ($registerLimit > ($authUser->consultant->student_register_limit - $authUser->consultant->student_register_number)) {
                return response()->json([
                    'data' => [
                        'error' => 'تعداد کاربر وارد شده بالاتر از سهمیه افزودن کاربر شما است.',
                    ],
                ], Response::HTTP_BAD_REQUEST);
            }
            return BonyadService::updateUserConsultant($parentIds, $registerLimit, 'sum');
        } else {
            if ($limit < $targetUser->consultant->student_register_limit) { //kam mishavad
                $registerLimit = $targetUser->consultant->student_register_limit - $limit;
                if ($registerLimit > ($targetUser->consultant->student_register_limit - $targetUser->consultant->student_register_number)) {
                    return response()->json([
                        'data' => [
                            'error' => 'تعداد کاربر وارد شده بالاتر از سهمیه فعال کاربر مورد نظر است.',
                        ],
                    ], Response::HTTP_BAD_REQUEST);
                }
                return BonyadService::updateUserConsultant($parentIds, $registerLimit, 'sub');
            }
        }
    }
}
