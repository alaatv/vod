<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait InInstalmentsTrait
{

    public function getPaidRatio()
    {
        return Cache::tags([$this->id.'_paid_ratio', 'userAsset_'.auth()?->id()])->remember($this->id.'_paid_ratio',
            config('constants.CACHE_600'), function () {
                $totalCost = $this->costwithcoupon + $this->costwithoutcoupon;
                $paidForNotInstallmentallyProducts = $this->calcNotInstallmentallyProductsPrice();
                $paidForInstallments = array_sum($this->transactions()->successful()->pluck('cost')->toArray()) - $paidForNotInstallmentallyProducts;
                return $paidForInstallments / ($totalCost - $paidForNotInstallmentallyProducts);
            });
    }

    public function calcNotInstallmentallyProductsPrice(): int
    {
        $notInstallmentallyProducts = $this->orderproducts()
            ->where('includedInInstalments', '=', 0)
            ->get()
            ->pluck('tmp_final_cost')
            ->toArray();

        return array_sum($notInstallmentallyProducts);
    }
}
