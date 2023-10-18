<?php

namespace App\Models;

use App\Traits\ModelsTraits\SmsProviderTraits\Scopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;
use Eloquent;

class SmsProvider extends Model
{
    use Scopes;

    const DEFAULT_RECEIVING_SMS_PROVIDER_ID = 1;

    public function operators()
    {
        return $this->hasMany(PhoneNumberProvider::class, 'provider_id', 'id');
    }
}
