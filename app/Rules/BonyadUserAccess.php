<?php

namespace App\Rules;

use App\Models\User;
use App\Repositories\UserRepo;
use App\Services\BonyadService;
use Illuminate\Contracts\Validation\Rule;

class BonyadUserAccess implements Rule
{

    private $authUser;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->authUser = $user;
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
        $authUser = $this->authUser;
        $roles = BonyadService::getRoles();
        $rolesKey = collect(array_keys($roles));
        $authUserRoles = $authUser->roles()->pluck('name');
        $userRole = $rolesKey->intersect($authUserRoles)->first();
        if (is_null($userRole)) {
            return false;
        }
        return UserRepo::userAccess($authUser->id, $value, $roles[$userRole]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'دسترسی به اطلاعات این کاربر برای شما مجاز نیست.';
    }
}
