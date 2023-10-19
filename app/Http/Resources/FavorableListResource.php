<?php

namespace App\Http\Resources;

use App\Models\Contentset;
use App\Models\Contentset;
use App\Models\Timepoint;
use App\Models\Timepoint;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FavorableListResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'order' => $this->order,
            'favors' => $this->getFavors(),
        ];
    }

    private function getFavors(): Collection
    {
        $favors = collect();
        $products = collect();
        $contents = collect();
        $contentSets = collect();
        $timePoints = collect();
        foreach ($this->favors as $favor) {
            if ($favor->favorable_type === 'App\Content') {
                $content = \App\Content::find($favor->favorable_id);
                $contents->push(new ContentWithFavoredTimePoints($content));
            } elseif ($favor->favorable_type === 'App\Contentset') {
                $contentSet = Contentset::find($favor->favorable_id);
                $contentSets->push(new SetInIndex($contentSet));
            } elseif ($favor->favorable_type === 'App\Product') {
                $product = \App\Product::find($favor->favorable_id);
                $products->push(new Product($product));
            } else {
                $timePoint = Timepoint::find($favor->favorable_id);
                $timePoints->push(new ContentTimePointWeb($timePoint));
            }
        }
        $favors->put('products', $products);
        $favors->put('contents', $contents);
        $favors->put('contentSets', $contentSets);
        $favors->put('timePoints', $timePoints);
        return $favors;
    }
}
