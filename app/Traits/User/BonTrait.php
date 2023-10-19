<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 16:30
 */

namespace App\Traits\User;




use App\Models\Userbon;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

trait BonTrait
{
    /**
     * @param  string  $bonName
     *
     * @return int
     */
    public function userHasBon($bonName = null): int
    {
        if (is_null($bonName)) {
            $bonName = config('constants.BON1');
        }
        $key = 'user:userHasBon:'.$this->cacheKey().'-'.$bonName;

        return Cache::tags(['user', 'bon', 'user_'.$this->id, 'user_'.$this->id.'_hasBon'])
            ->remember($key, config('constants.CACHE_60'), function () use ($bonName) {

                $bon = Bon::all()
                    ->where('name', $bonName)
                    ->where('isEnable', '=', 1);
                if ($bon->isEmpty()) {
                    return false;
                }
                /** @var Userbon $userbons */
                $userbons = $this->userValidBons($bon->first());
                $totalBonNumber = 0;
                foreach ($userbons as $userbon) {
                    $totalBonNumber = $totalBonNumber + ($userbon->totalNumber - $userbon->usedNumber);
                }

                return $totalBonNumber;
            });
    }

    /**
     * returns user valid bons of the specified bons
     *
     * @param  Bon  $bon
     * @param  User  $user
     *
     * @return  Collection a collection of user valid bons of specified bon
     */
    public function userValidBons(Bon $bon)
    {
        $key = 'user:userValidBons:'.$this->cacheKey().'-'.(isset($bon) ? $bon->cacheKey() : '');

        return Cache::tags(['user', 'bon', 'user_'.$this->id, 'user_'.$this->id.'_validBons'])
            ->remember($key, config('constants.CACHE_60'), function () use ($bon) {
                return Userbon::where('user_id', $this->id)
                    ->where('bon_id', $bon->id)
                    ->where('userbonstatus_id',
                        config('constants.USERBON_STATUS_ACTIVE'))
                    ->whereColumn('totalNumber', '>', 'usedNumber')
                    ->where(function ($query) {
                        /** @var QueryBuilder $query */
                        $query->whereNull('validSince')
                            ->orwhere('validSince', '<', Carbon::now());
                    })
                    ->where(function ($query) {
                        /** @var QueryBuilder $query */
                        $query->whereNull('validUntil')
                            ->orwhere('validUntil', '>', Carbon::now());
                    })
                    ->get();
            });
    }

    public function userbons()
    {
        return $this->hasMany(Userbon::class);
    }
}
