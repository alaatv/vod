<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-10
 * Time: 16:05
 */

namespace App\Classes\Search\Tag;

use App\Classes\Taggable;

interface TaggingInterface
{
    public function setTags($taggableId, array $tags, $score = 0);

    /**
     * @param $taggableId
     *
     * @return array
     */
    public function getTags($taggableId): array;

    /**
     * @param  array  $tags
     *
     * @return Taggable
     */
    public function getTaggable(array $tags): array;
}
