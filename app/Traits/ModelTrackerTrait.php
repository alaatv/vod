<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-07
 * Time: 14:33
 */

namespace App\Traits;

trait ModelTrackerTrait
{
    /**
     * Set th content's page_view.
     *
     * @param $input
     */
    public function setPageViewAttribute($input)
    {
        $this->attributes['page_view'] = json_encode($input, JSON_UNESCAPED_UNICODE);
    }

    /**
     *
     * @param $value
     *
     * @return mixed
     */
    public function getPageViewAttribute($value)
    {
        return optional(json_decode($value))->page_views;
    }
}
