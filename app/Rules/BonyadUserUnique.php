<?php

namespace App\Rules;

use App\Models\User;
use App\Services\BonyadService;
use Illuminate\Contracts\Validation\Rule;
use Request;
use function RingCentral\Psr7\str;

class BonyadUserUnique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $request = Request::all();
        if (strpos($attribute, '.')) {
            $arr = explode('.', $attribute);
            $nationalCode = $request['users'][$arr[1]]['nationalCode'];
        } else {
            $nationalCode = $request['nationalCode'];
        }
        $user = User::where('mobile', $value)->where('nationalCode', $nationalCode)->whereHas('roles',
            function ($query) {
                return $query->whereIn('name', array_keys(BonyadService::getRoles()));
            })->get();
        return $user->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'کاربر با گروه بندی دیگری وجود دارد';
    }
}
