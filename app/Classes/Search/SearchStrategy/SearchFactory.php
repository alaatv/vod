<?php


namespace App\Classes\Search\SearchStrategy;


use Illuminate\Container\Container;

class SearchFactory
{
    public static function factory(?string $q = '')
    {
//        if($q){
//            return new SearchiaSearch();
//        }
        return Container::getInstance()->make(AlaaSearch::class);

    }
}
