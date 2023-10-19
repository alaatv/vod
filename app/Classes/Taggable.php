<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-08-21
 * Time: 18:11
 */

namespace App\Classes;

interface Taggable
{
    public function retrievingTags();

    public function getTaggableTags();

    public function getTaggableId();

    public function getTaggableScore();

    public function isTaggableActive(): bool;
}
