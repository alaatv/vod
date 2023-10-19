<?php

namespace App\Rules;

use App\Models\User;
use App\Repositories\NetworkMarketingRepo;
use Illuminate\Contracts\Validation\ImplicitRule;

class YaldaReferralCode implements ImplicitRule
{
    /**
     * @var string
     */
    private $message;

    public function __construct(private ?User $user)
    {
    }

    public function passes($attribute, $value)
    {
        if ($this->user === null) {
            $this->message = 'validation.should_auth';
            return false;
        }

        $instance = NetworkMarketingRepo::getReferralCodeInstance($value,
            config('constants.EVENTS.YALDA_1400'))->first();

        if (optional($instance)->owner_id === $this->user->id) {
            $this->message = 'validation.owner';
            return false;
        }

        if (NetworkMarketingRepo::getReferralCodeUserInstance($this->user->id, config('constants.EVENTS.YALDA_1400'))) {
            $this->message = 'validation.unique_yalda_code';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans($this->message);
    }

}

