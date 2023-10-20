<?php
/**
 * Created by PhpStorm.
 * User: alireza
 * Date: 18/09/2021
 * Time: 5:45 PM
 */

namespace App\Collection;


use Illuminate\Database\Eloquent\Collection;

class VoucherCollections extends Collection
{
    public function groupByUserId()
    {
        return $this->unique('user_id');
    }
}
