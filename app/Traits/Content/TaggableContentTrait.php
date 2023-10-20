<?php


namespace App\Traits\Content;


use App\Classes\Search\Tag\ContentTagManagerViaApi;

trait TaggableContentTrait
{
    /**
     * Retrieves content's tags
     *
     * @return array
     */
    public function retrievingTags(): array
    {
        return (new ContentTagManagerViaApi())->getTags($this->id);
    }

    public function getTaggableTags()
    {
        return array_merge((optional($this->tags)->tags ?? []), $this->forrest_tree_tags ?? [],
            $this->forrest_tree_grid ?? []);
    }

    public function getTaggableForrest()
    {
        return $this->forrest_tree_grid ?? [];
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
        if ($this->isEnable() && ((isset($this->tags) && !empty($this->tags->tags)) || isset($this->forrest_tree_grid) || isset($this->forrest_tree_tags))) {
            return true;
        }

        return false;
    }
}
