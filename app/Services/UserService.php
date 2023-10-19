<?php

namespace App\Services;

use App\Repositories\UserRepo;

class UserService
{
    public function __construct(public UserRepo $userRepo)
    {
    }

    public function checkUserCanGetEntekhabReshteAbrisham1($abrisham1ProductIds): bool
    {
        return auth()->user()->orders()->paidAndClosed()->whereHas('orderproducts',
            function ($q) use ($abrisham1ProductIds) {
                $q->whereIn('product_id', $abrisham1ProductIds);
            })->exists();
    }

    public function checkUserCanGetEntekhabReshteAbrishamPro($abrishamProProductIds): bool
    {
        return auth()->user()->orders()->paidAndClosed()->whereHas('orderproducts',
            function ($q) use ($abrishamProProductIds) {
                $q->whereIn('product_id', $abrishamProProductIds);
            })->exists();
    }
}
