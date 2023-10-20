<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-02
 * Time: 17:37
 */

namespace App\Classes\Format;

use App\Collection\BlockCollection;
use Illuminate\Support\Collection;

interface BlockCollectionFormatter
{
    public function format(BlockCollection $blocks): Collection;
}
