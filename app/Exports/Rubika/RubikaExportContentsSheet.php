<?php

namespace App\Exports\Rubika;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Contentset;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RubikaExportContentsSheet implements FromArray, WithTitle, WithHeadings
{
    public function __construct(private Contentset $set)
    {
    }

    public function array(): array
    {
        return $this->set->contents()
            ->active()
            ->notRedirected()
            ->video()
            ->without(['set'])
            ->get(['id', 'order', 'name', 'description', 'duration', 'file', 'contentset_id', 'isFree', 'thumbnail'])
            ->sortBy('order')
            ->map(function (Content $content) {
                $reportableData = $this->makeupContent($content);
                return $reportableData;

            })->toArray();
    }

    private function makeupContent(Content $content)
    {
        $reportableData['order'] = $content->order;
        $reportableData['name'] = $content->name;
        $reportableData['description'] = implode(' ', explode("\n", strip_tags($content->description)));

        $reportableData['link'] = route('c.show', ['c' => $content]);
        $reportableData['duration'] = $content->duration;

        $videos = $content->file_for_admin['video'] ?? [];
        foreach ($videos as $video) {
            if ($video->caption == 'کیفیت عالی') {
                $reportableData['heightQualityLink'] = removeQueryFromUrl($video->link);
                $reportableData['heightQualitySize'] = $video->size;
            } elseif ($video->caption == 'کیفیت بالا') {
                $reportableData['midleQualityLink'] = removeQueryFromUrl($video->link);
                $reportableData['midleQualitySize'] = $video->size;
            } elseif ($video->caption == 'کیفیت متوسط') {
                $reportableData['lowQualityLink'] = removeQueryFromUrl($video->link);
                $reportableData['lowQualitySize'] = $video->size;
            }
        }

        $reportableData['thumbnail'] = $content->thumbnail;
        return $reportableData;
    }

    public function title(): string
    {
        return $this->set->name;
    }

    public function headings(): array
    {
        return [
            'ترتیب', 'عنوان', 'توضیحات', 'لینک صفحه تماشا', 'مدت زمان(ثانیه)', 'لینک دانلود کیفیت عالی',
            'حجم کیفیت عالی(مگ)', 'لینک دانلود کیفیت بالا', 'حجم کیفیت بالا(مگ)', 'لینک دانلود کیفیت متوسط',
            'حجم کیفیت متوسط(مگ)', 'عکس تامبنیل'
        ];
    }
}
