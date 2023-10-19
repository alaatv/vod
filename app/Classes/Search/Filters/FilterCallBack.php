<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-10
 * Time: 19:24
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

interface FilterCallBack
{
    /**
     * @param  array  $err  [ "status" => integer, "message" => string , "data" => mix]
     *
     * @return void
     */
    public function err(array $err): void;

    public function success(Builder &$builder, &$data);
}
