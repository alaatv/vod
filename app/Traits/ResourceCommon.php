<?php


namespace App\Traits;


use App\Http\Resources\Tag;

trait ResourceCommon
{
    private function getTag()
    {
        return new Tag($this->tags);
    }
}
