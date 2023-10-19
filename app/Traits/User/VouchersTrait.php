<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 16:43
 */

namespace App\Traits\User;


use App\Models\Productvoucher;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait VouchersTrait
{
    /**
     * Retrieve all product vouchers of this user
     *
     * @return HasMany
     */
    public function productvouchers()
    {
        return $this->hasMany(Productvoucher::class);
    }
}
