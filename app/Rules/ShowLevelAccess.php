<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class ShowLevelAccess implements Rule
{
    private $authUser;
    private $permissions;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->authUser = $user;
        $this->permissions = [
            'show-networks' => config('constants.BONYAD_EHSAN_SHOW_NETWORKS'),
            'show-subnetworks' => config('constants.BONYAD_EHSAN_SHOW_SUBNETWORKS'),
            'show-moshavers' => config('constants.BONYAD_EHSAN_SHOW_MOSHAVERS'),
            'show-students' => config('constants.BONYAD_EHSAN_SHOW_STUDENTS'),
        ];
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
        return $this->authUser->hasPermission($this->permissions[$value]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'دسترسی مجاز نیست.';
    }
}
