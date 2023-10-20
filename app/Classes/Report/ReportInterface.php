<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-11-03
 * Time: 11:28
 */

namespace App\Classes\Report;

interface ReportInterface
{
    public function getReport($path, $from, $to);
    /*    public function getMostViewedContent($from, $to);
        public function getMostViewedProduct($from, $to);*/
}
