<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-17
 * Time: 18:51
 */

namespace App\Classes\Search\Tag;

class TicketTagManagerViaApi extends RedisTagManagerViaApi
{
    protected $bucket = 'ticket';

    /**
     * RedisTagViaApi constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->limit_PerPage = 1000000;
        $this->limit_WithScores = 1;
        $this->limit_PageNum = 1;
    }
}
