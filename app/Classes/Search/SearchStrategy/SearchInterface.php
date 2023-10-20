<?php


namespace App\Classes\Search\SearchStrategy;


interface SearchInterface
{
    public function search(array $request): array;
}
