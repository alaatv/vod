<?php

namespace App\Traits\LiveDescription;

use App\Classes\Search\Tag\LiveDescriptionTagManagerViaApi;

trait TaggableLiveDescriptionTrait
{
    public function retrievingTags()
    {
        return (new LiveDescriptionTagManagerViaApi())->getTags($this->id);
    }

    public function getTaggableTags()
    {
        return $this->tags;
    }

    public function getTaggableId(): int
    {
        return $this->id;
    }

    public function getTaggableScore()
    {
        return isset($this->created_at) ? $this->created_at->timestamp : null;
    }

    public function isTaggableActive(): bool
    {
        if ($this->isActive() && isset($this->tags)) {
            return true;
        }

        return false;
    }
}
