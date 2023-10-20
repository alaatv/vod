<?php

namespace App\Http\Resources;

use App\Collection\ContentCollection;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ProductSetLiteResource
 * @package App\Http\Resources
 * @mixin Contentset
 */
class ProductSetLiteResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Contentset)) {
            return [];
        }

        return [
            'id' => $this->id,
            'short_title' => $this->when(isset($this->small_name), $this->small_name),
            'sections' => $this->when(isset($this->contents),
                \App\Http\Resources\Section::collection($this->getSections($this->contents))),
            'contents_count' => $this->active_contents_count,
            'contents_duration' => $this->active_contents_duration,
            'order' => $this->pivot->order,
        ];
    }

    /**
     * @param  ContentCollection  $contents
     * @return array
     */
    private function getSections(ContentCollection $contents): Collection
    {
        $sections = [];
        $sectionCollection = collect();
        /** @var Content $content */
        foreach ($contents as $content) {
            if (is_null($content->section_id)) {
                continue;
            }
            /** @var Section $section */
            $section = $content->section;
            $resultFieldName = 'title';
            if (!in_array($section->name, array_column($sections, $resultFieldName))) {
                $sections[] = [$resultFieldName => $section->name];
                $sectionCollection->push($section);
            }

        }
        return $sectionCollection;
    }
}
