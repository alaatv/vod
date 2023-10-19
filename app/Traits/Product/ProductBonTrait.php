<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-03-08
 * Time: 20:20
 */

namespace App\Traits\Product;



use App\Models\Bon;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait ProductBonTrait
{

    /**
     * @param $bon
     *
     * @return bool
     */
    public function canApplyBon(Bon $bon): bool
    {
        /** @var Collection $bon */
        return (!($this->isFree || ($this->hasParents() && $this->parents()->first()->isFree)) && ($this->basePrice != 0) && $bon->pivot->discount > 0);
    }

    /**
     * Checks whether this product has this bon or not
     *
     * @param  int  $bonId
     *
     * @return bool
     */
    public function hasBon(int $bonId): bool
    {
        $key = 'product:hasBon:'.$this->cacheKey().'_bonId:'.$bonId;

        return Cache::tags(['product', 'bon', 'product_'.$this->id, 'product_'.$this->id.'_bons'])
            ->remember($key, config('constants.CACHE_600'), function () use ($bonId) {
                return $this->bons->where('id', $bonId)
                    ->isNotEmpty();
            });
    }

    public function calculateBonPlus($bonId): int
    {
        $key = 'product:bonPlus:'.$this->cacheKey().'_bonId:'.$bonId;

        return (int) Cache::tags(['product', 'bon', 'product_'.$this->id, 'product_'.$this->id.'_bons'])
            ->remember($key, config('constants.CACHE_600'), function () use ($bonId) {
                $bonPlus = 0;
                $bonPlus += $this->bons->where('id', $bonId)
                    ->sum('pivot.bonPlus');
                if ($bonPlus != 0) {

                    return $bonPlus;
                }
                $parents = $this->getAllParents();
                if ($parents->isNotEmpty()) {
                    foreach ($parents as $parent) {
                        $bonPlus += $parent->bons->where('id', $bonId)
                            ->sum('pivot.bonPlus');
                    }
                }


                return $bonPlus;
            });
    }

    /**
     * Obtains product's bon discount percentage
     *
     * @param $bonName
     *
     * @return float|int
     */
    public function obtainBonDiscount($bonName)
    {
        $key = 'product:bonDiscount:'.$this->cacheKey().'_bonName:'.$bonName;

        return Cache::tags(['product', 'bon', 'product_'.$this->id, 'product_'.$this->id.'_bons'])
            ->remember($key, config('constants.CACHE_10'), function () use ($bonName) {
                $discount = 0;
                $bons = $this->getTotalBons($bonName);
                if ($bons->isNotEmpty()) {
                    $bon = $bons->first();
                    $discount = $bon->pivot->discount;
                }


                return $discount / 100;
            });
    }

    /**
     * Gets products total bons = also checks whether his parents have any bons or not
     *
     * @param $bonName
     *
     * @return Collection
     */
    public function getTotalBons($bonName): Collection
    {
        $key = 'product:getTotalBons:'.$this->cacheKey().'_boneName:'.$bonName;

        return Cache::tags(['product', 'bon', 'product_'.$this->id, 'product_'.$this->id.'_bons'])
            ->remember($key, config('constants.CACHE_600'), function () use ($bonName) {
                $bons = $this->getBons($bonName);
                if (!$bons->isEmpty()) {

                    return $bons;
                }
                $parents = $this->getAllParents();
                foreach ($parents as $parent) {
                    // ToDo : It does not check parents in a hierarchy to the root

                    /** @var Product $parent */
                    $bons = $parent->getBons($bonName);
                    if ($bons->isNotEmpty()) {
                        break;
                    }
                }


                return $bons;
            });
    }

    /**
     * Gets product's bon collection and filters it by bon name and enable/disable
     *
     * @param  string  $bonName
     * @param  int  $enable
     *
     * @return Collection
     */
    public function getBons($bonName = '', $enable = 1): Collection
    {
        $key = 'product:getBons:'.$this->cacheKey().'_boneName:'.$bonName;

        return Cache::tags(['product', 'bon', 'product_'.$this->id, 'product_'.$this->id.'_bons'])
            ->remember($key, config('constants.CACHE_600'), function () use ($bonName, $enable) {
                /** @var Bon $bons */
                $bons = $this->bons();
                if (strlen($bonName) > 0) {
                    $bons = $bons->where('name', $bonName);
                }

                if ($enable) {
                    $bons = $bons->enable();
                }


                return $bons->get();
            });
    }

    /**
     * @return BelongsToMany
     */
    public function bons()
    {
        return $this->belongsToMany(Bon::class)
            ->withPivot('discount', 'bonPlus');
    }

}
