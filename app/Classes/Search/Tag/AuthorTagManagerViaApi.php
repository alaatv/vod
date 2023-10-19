<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-10
 * Time: 16:08
 */

namespace App\Classes\Search\Tag;

class AuthorTagManagerViaApi extends RedisTagManagerViaApi
{
    protected $bucket = 'author';

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
