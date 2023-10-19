<?php

namespace App\Traits;

use App\Classes\Search\SearchStrategy\SearchiaSearch;
use App\Models\Contentset;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;


//use App\Classes\TagsGroup;

trait SearchiaCommonTrait
{
    private function getFromSearchia()
    {
        $type = Arr::last(explode("\\", get_class($this->model)));
        $id = $this->model->id;

        $filters = "filters=(type:$type) and (id=$id)";
        $query = SearchiaSearch::MAIN_URL
            .SearchiaSearch::INDEX
            ."?query=&$filters";
        $response = json_decode(Http::withHeaders(SearchiaSearch::HEADERS)
            ->get($query)
            ->body());
        if ($response?->entity?->totalHits === 1) {
            return $response->entity->results[0]?->documentId;
        }
        return null;
    }

    private function makeDocument(): array
    {
//        $tags_groups = (new TagsGroup($this->model?->tags?->tags ?? []))->getTagsGroup()->toArray();
        $tags_groups = [];
        $document = [];
        $document['id'] = $this->model->id;
        $document['type'] = \Arr::last(explode('\\', get_class($this->model)));
        $document['title'] = $this->model->name;
        $document['educational_system_tags'] = $tags_groups[1] ?? [];
        $document['grade_tags'] = $tags_groups[2] ?? [];
        $document['major_tags'] = $tags_groups[3] ?? [];
        $document['lesson_tags'] = $tags_groups[4] ?? [];
        $document['teacher_tags'] = $tags_groups[5] ?? [];
        $document['other_tags'] = $tags_groups[6] ?? [];

        $document['description'] = '';
        $modelType = get_class($this->model);
        if ($modelType == Product::class) {
            $document['description'] = $this->model->shortDescription.' '.$this->model->longDescription;
        } elseif ($modelType == Content::class || $modelType == Contentset::class) {
            $document['description'] = $this->model->description;
        }

        return $document;
    }
}
