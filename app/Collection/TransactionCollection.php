<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-03
 * Time: 19:06
 */

namespace App\Collection;

use App\Traits\JsonResponseFormat;
use Illuminate\Database\Eloquent\Collection;

class TransactionCollection extends Collection
{
    use JsonResponseFormat;
}
