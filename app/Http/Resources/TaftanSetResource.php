<?php

namespace App\Http\Resources;

use App\Http\Controllers\Web\TaftanDashboardPageController as Taftan;
use App\Models\Contentset;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class TaftanSetResource extends JsonResource
{

    public function toArray($request)
    {
        if (!($this->resource instanceof Contentset)) {
            return [];
        }

        return [
            'id' => $this->id,
            'title' => $this->name,
            'product_id' => $this->setProductId(),
            'sections' => $this->setSections(),
            'major' => $this->setMajor(),
            'short_title' => $this->when(isset($this->small_name), $this->small_name),
        ];
    }

    private function setProductId()
    {
        $productInfo = Arr::get(Taftan::MAP, $this->id);
        return Arr::get($productInfo, 'product');
    }

    private function setSections()
    {
        return $this->sections()
            ->unique()
            ->sortBy('order')
            ->map(function (\App\Section $section) {
                $requiredFields['id'] = $section->id;
                $requiredFields['title'] = $section->name;
                return $requiredFields;
            })->toArray();
    }

    private function setMajor()
    {
        $majorInfo = Arr::get(Taftan::MAP, $this->id);
        $majorId = Arr::get($majorInfo, 'major');
        $majors = array_filter(Taftan::MAJORS, fn($major) => $major['id'] == $majorId);

        return [
            'id' => $majorId,
            'title' => Arr::get(array_values($majors)[0] ?? [], 'title'),
        ];
    }
}
