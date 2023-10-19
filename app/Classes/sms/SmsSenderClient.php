<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-10-17
 * Time: 18:12
 */

namespace App\Classes\sms;

interface SmsSenderClient
{
    /**
     * @param  array  $params
     *
     * @return array
     */
    public function send(array $params);
}
