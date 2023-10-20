<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-08-19
 * Time: 17:45
 */

namespace App\Classes;

use Illuminate\Support\Collection;

interface Advertisable
{
    public function getAddItems(): Collection;
}
