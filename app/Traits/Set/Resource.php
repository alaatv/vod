<?php


namespace App\Traits\Set;


use App\Http\Resources\Author;

trait Resource
{
    private function getAuthor()
    {
        if (is_null($this->author)) {
            return null;
        }

        return new Author($this->author);
    }

    private function hasUrl()
    {
        return isset($this->url) || isset($this->api_url_v2);
    }
}
