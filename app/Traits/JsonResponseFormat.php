<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 2/17/2019
 * Time: 4:44 PM
 */

namespace App\Traits;



use App\Models\Orderproduct;

trait JsonResponseFormat
{
    /**
     *  call setVisible for every item
     *
     * @param  array  $attributes
     */
    public function setVisible(array $attributes): void
    {
        /** @var Orderproduct $item */
        foreach ($this as $item) {
            $item->setVisible($attributes);
        }
    }
}
