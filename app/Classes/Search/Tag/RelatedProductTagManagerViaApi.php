<?php


namespace App\Classes\Search\Tag;


class RelatedProductTagManagerViaApi extends RedisTagManagerViaApi
{
    protected $bucket = 'relatedproduct';

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
