<?php

namespace App\Classes\Search\Tag;

class MapDetailTagManagerViaApi extends RedisTagManagerViaApi
{
    protected $bucket = 'mapDetail';

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
