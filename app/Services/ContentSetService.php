<?php

namespace App\Services;

use App\Models\Contentset;
use App\Models\Source;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class ContentSetService
{
    public function fillContentSet(array $inputData, Contentset $contentSet)
    {
        $enabled = Arr::has($inputData, 'enable') ? Arr::get($inputData, 'enable') : 0;
        $display = Arr::has($inputData, 'display') ? Arr::get($inputData, 'display') : 0;
        $tagString = Arr::get($inputData, 'tags');
        $redirectUrl = Arr::get($inputData, 'redirectUrl', null);
        $redirectCode = Arr::get($inputData, 'redirectCode', null);
        if (isset($redirectUrl) && isset($redirectCode)) {
            $inputData['redirectUrl'] = [
                'url' => $redirectUrl,
                'code' => $redirectCode,
            ];
        }

        $contentSet->fill($inputData);
        $contentSet->tags = convertTagStringToArray($tagString);

        $contentSet->enable = $enabled;
        $contentSet->display = $display;

        if (Arr::has($inputData, 'photo')) {
            $contentSet->setPhoto(Arr::get($inputData, 'photo'), config('disks.SET_IMAGE_MINIO'));
        }

        if (isset($contentSet->redirectUrl)) {
            $contentSet->display = 0;
        }

        if (Arr::has($inputData, 'forrest_tree')) {
            $contentSet->forrest_tree_grid = Arr::get($inputData, 'forrest_tree');
        }

        if (Arr::has($inputData, 'forrest_tree_tags')) {
            $contentSet->forrest_tree_tags = Arr::get($inputData, 'forrest_tree_tags');
        }

        if (!Arr::has($inputData, 'sources_id')) {
            return;
        }
    }

    public function syncSources(array $inputData, Contentset $contentSet)
    {
        $sources = Source::whereIn('id', Arr::get($inputData, 'sources_id', []))->get();
        if (!$sources->isNotEmpty()) {
            return;
        }
        $contentSet->sources()->sync($sources);
        if (Arr::has($inputData, 'attachSourceToContents')) {
            $sourcesId = Arr::get($inputData, 'sources_id');
            $contentSet->contents->attachSource($sourcesId);
        }
    }

    public function syncProducts(array $products, Contentset $contentSet)
    {
        foreach ($contentSet->products as $product) {
            Cache::tags(['product_'.$product->id.'_sets'])->flush();
        }

        $contentSet->products()->detach();
        $contentSet->products()->attach($products);

        foreach ($products as $productId) {
            Cache::tags(['product_'.$productId.'_sets'])->flush();
        }
    }

    public function toggleProduct(int $productId, Contentset $contentSet, $order)
    {
        foreach ($contentSet->products as $product) {
            Cache::tags(['product_'.$product->id.'_sets'])->flush();
        }
        $contentSet->products()->toggle([
            $productId => ['order' => $order]
        ]);
        Cache::tags(['product_'.$productId.'_sets'])->flush();
    }
}
