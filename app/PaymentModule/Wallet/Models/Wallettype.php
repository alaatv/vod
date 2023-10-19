<?php

namespace App\PaymentModule\Wallet\Models;

use App\Models\BaseModel;
use App\Models\BaseModel;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Wallettype
 *
 * @property int $id
 * @property string $name        نام
 * @property string|null $displayName نام قابل نمایش
 * @property string|null $description توضیح کوتاه
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|\App\Wallet[] $wallets
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Wallettype onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|\App\Wallettype whereCreatedAt($value)
 * @method static Builder|\App\Wallettype whereDeletedAt($value)
 * @method static Builder|\App\Wallettype whereDescription($value)
 * @method static Builder|\App\Wallettype whereDisplayName($value)
 * @method static Builder|\App\Wallettype whereId($value)
 * @method static Builder|\App\Wallettype whereName($value)
 * @method static Builder|\App\Wallettype whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Wallettype withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Wallettype withoutTrashed()
 * @mixin Eloquent
 * @method static Builder|\App\Wallettype newModelQuery()
 * @method static Builder|\App\Wallettype newQuery()
 * @method static Builder|\App\Wallettype query()
 * @method static Builder|BaseModel disableCache()
 * @method static Builder|BaseModel withCacheCooldownSeconds($seconds)
 * @property-read mixed $cache_cooldown_seconds
 * @property-read int|null $wallets_count
 * @property-read string|null $cache_clear_url
 * @property-read mixed $date_time
 */
class Wallettype extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
}
