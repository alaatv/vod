<?php

namespace App\Models;

use App\Traits\ModelsTraits\SmsProviderTraits\Scopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;
use Eloquent;

/**
 * App\SmsProvider
 *
 * @property int $id
 * @property string $number شماره سامانه پیامکی
 * @property int $cost هزینه ارسال با شماره سامانه مورد نظر
 * @property int $enable فعال یا غیرفعال بودن شماره سامانه
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|SmsProvider newModelQuery()
 * @method static Builder|SmsProvider newQuery()
 * @method static Builder|SmsProvider query()
 * @method static Builder|SmsProvider whereCost($value)
 * @method static Builder|SmsProvider whereCreatedAt($value)
 * @method static Builder|SmsProvider whereDeletedAt($value)
 * @method static Builder|SmsProvider whereDescription($value)
 * @method static Builder|SmsProvider whereEnable($value)
 * @method static Builder|SmsProvider whereId($value)
 * @method static Builder|SmsProvider whereNumber($value)
 * @method static Builder|SmsProvider whereUpdatedAt($value)
 * @mixin Eloquent
 */
class SmsProvider extends Model
{
    use Scopes;

    const DEFAULT_RECEIVING_SMS_PROVIDER_ID = 1;

    public function operators()
    {
        return $this->hasMany(PhoneNumberProvider::class, 'provider_id', 'id');
    }
}
