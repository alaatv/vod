<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-08-21
 * Time: 15:53
 */

namespace App\Collection;

use App\Traits\CollectionTrait;
use App\Traits\JsonResponseFormat;
use Illuminate\Database\Eloquent\Collection;

class SetCollection extends Collection
{
    use CollectionTrait;
    use JsonResponseFormat;
}
