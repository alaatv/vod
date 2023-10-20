<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-08-21
 * Time: 15:53
 */

namespace App\Collection;

use App\Models\Content;
use App\Traits\CollectionTrait;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use LogicException;

class ContentCollection extends Collection
{

    use CollectionTrait;


    public function videos()
    {
        return $this->where('contenttype_id', Content::CONTENT_TYPE_VIDEO);
    }

    public function pamphlets()
    {
        return $this->where('contenttype_id', Content::CONTENT_TYPE_PAMPHLET);
    }

    public function articles()
    {
        return $this->where('contenttype_id', Content::CONTENT_TYPE_ARTICLE);
    }

    public function flashcards()
    {
        throw new LogicException('define Content::CONTENT_TYPE_FLASHCARD');
    }

    public function onlyItemTypes(array $itemTypes)
    {
        $result = new self();
        foreach ($itemTypes as $itemType) {
            $filter = $itemType.'s';
            $result = $result->merge($this->$filter());
        }

        return $result;
    }

    public function normalMates(): BaseCollection
    {
        $items = $this;
        $result = collect();

        foreach ($items as $content) {

            $myContentType = optional($content->contenttype)->name;
            $result->push([
                'content' => $content,
                'type' => $myContentType,
                'thumbnail' => $content->thumbnail,
                'session' => $content->session,
            ]);
        }

        return $result;
    }

    public function attachSource(array $sourcesId)
    {
        foreach ($this as $content) {
            foreach ($sourcesId as $sourceId) {
                /** @var Content $content */
                $content->attachSource($sourceId);
            }
        }
    }
}
