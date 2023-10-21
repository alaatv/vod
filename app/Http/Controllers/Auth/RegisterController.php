<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\{CharacterCommon, Helper, RedirectTrait, RequestCommon, UserCommon};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use CharacterCommon;
    use Helper;
    use UserCommon;
    use RequestCommon;
    use RedirectTrait;

    /**
     * Create a new controller instance.
     *
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest');
        $this->middleware('convert:mobile|password|nationalCode');
        $request->offsetSet('userstatus_id', $request->get('userstatus_id', 2));
    }

    /**
     * overriding method
     * Show the application registration form.
     *
     * @return Response
     */
    public function showRegistrationForm()
    {
        $login = true;
        $voucher = false;
        $verifyMobile = false;
        $redirectUrl = route('web.index');

        return view('auth.voucherLogin', compact('redirectUrl', 'verifyMobile', 'voucher', 'login'));
    }

    /**
     * The user has been registered.
     *
     * @param  Request  $request
     * @param  mixed  $user
     *
     * @return mixed
     */
    protected function registered(Request $request, User $user)
    {
        if (!$request->expectsJson()) {
            return null;
        }
        $token = $user->getAppToken();
        $data = array_merge([
            'user' => $user,
        ], $token);

        return response()->json([
            'status' => 1,
            'msg' => 'user registered',
            'redirectTo' => $this->redirectTo($request),
            'data' => $data,
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $totalUserrules = $this->getInsertUserValidationRules($data);
        $rules = [
            'mobile' => $totalUserrules['mobile'],
            'nationalCode' => $totalUserrules['nationalCode'],
        ];

        return Validator::make($data, $rules);
    }

    protected function create(array $data)
    {
        return User::create([
            'firstName' => Arr::get($data, 'firstName'),
            'lastName' => Arr::get($data, 'lastName'),
            'mobile' => Arr::get($data, 'mobile'),
            'email' => Arr::get($data, 'email'),
            'nationalCode' => Arr::get($data, 'nationalCode'),
            'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
            'photo' => Arr::get($data, 'photo',
                config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE')),
            'password' => bcrypt(Arr::get($data, 'password', Arr::get($data, 'nationalCode'))),
            'major_id' => Arr::get($data, 'major_id'),
            'gender_id' => Arr::get($data, 'gender_id'),
        ]);
    }
}
