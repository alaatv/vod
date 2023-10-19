<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Cache;
use Illuminate\Http\Request;

class BlockRelationsController extends Controller
{
    public function attachProducts(Block $block, Request $request)
    {
        $request->validate(['attachable_products' => 'required|array|exists:products,id']);
        $block->products()->detach($request->get('attachable_products', []));
        $block->products()->attach($request->get('attachable_products', []));
        Cache::flush();
        return $request->get('attachable_products', []);
    }

    public function products(Block $block)
    {
        if (\request()->get('with_paginate', false)) {
            $response = $block->products()->paginate();
        } else {
            $response = $block->products()->get();
        }

        foreach ($response as $item) {
            $item->order = $item->pivot->order;
            $item->makeHidden([
                'category', 'financial_category_id', 'redirectUrl', 'discount_in_instalment_purchase', 'isFree',
                'amount', 'shortDescription', 'longDescription', 'specialDescription', 'tags', 'recommender_contents',
                'sample_contents', 'slogan', 'enable', 'display', 'has_instalment_option', 'page_view', 'updated_at',
                'price', 'children', 'bons',
            ]);
        }

        return $response;

    }

    public function detachProducts(Block $block, Request $request)
    {
        $request->validate(['detachable_products' => 'required|array|exists:products,id']);
        $block->products()->detach($request->get('detachable_products', []));
        Cache::flush();
        return $request->get('detachable_products', []);
    }

    public function attachSets(Block $block, Request $request)
    {
        $request->validate(['attachable_sets' => 'required|array|exists:contentsets,id']);
        $block->sets()->detach($request->get('attachable_sets', []));
        $block->sets()->attach($request->get('attachable_sets', []));
        Cache::flush();
        return $request->get('attachable_sets', []);
    }

    public function sets(Block $block)
    {
        if (\request()->get('with_paginate', false)) {
            $response = $block->sets()->paginate();
        } else {
            $response = $block->sets()->get();
        }

        foreach ($response as $item) {
            $item->makeHidden([
                'redirectUrl', 'author_id', 'description', 'photo', 'tags', 'enable', 'display', 'created_at',
                'updated_at', 'contents_count', 'active_contents_count', 'url', 'apiUrl', 'shortName', 'author',
                'contentUrl', 'setUrl',
            ]);
            $item->order = $item->pivot->order;
        }

        return $response;
    }

    public function detachSets(Block $block, Request $request)
    {
        $request->validate(['detachable_sets' => 'required|array|exists:contentsets,id']);
        $block->sets()->detach($request->get('detachable_sets', []));
        Cache::flush();
        return $request->get('detachable_sets', []);
    }

    public function attachContents(Block $block, Request $request)
    {
        $request->validate(['attachable_contents' => 'required|array|exists:educationalcontents,id']);
        $block->contents()->detach($request->get('attachable_contents', []));
        $block->contents()->attach($request->get('attachable_contents', []));
        Cache::flush();
        return $request->get('attachable_contents', []);
    }

    public function contents(Block $block)
    {
        if (\request()->get('with_paginate', false)) {
            $response = $block->contents()->paginate();
        } else {
            $response = $block->contents()->get();
        }

        foreach ($response as $item) {
            $item->makeHidden([
                'redirectUrl', 'contenttype_id', 'section_id', 'copied_from', 'description', 'tmp_description', 'tags',
                'context', 'file', 'duration', 'thumbnail', 'isFree', 'page_view', 'enable', 'display', 'validSince',
                'created_at', 'updated_at', 'pivot', 'set', 'contenttype'
            ]);
            $item->order = $item->pivot->order;
        }

        return $response;
    }

    public function detachContents(Block $block, Request $request)
    {
        $request->validate(['detachable_contents' => 'required|array|exists:educationalcontents,id']);
        $block->contents()->detach($request->get('detachable_contents', []));
        Cache::flush();
        return $request->get('detachable_contents', []);
    }

    public function attachBanners(Block $block, Request $request)
    {
        $request->validate(['attachable_banners' => 'required|array|exists:slideshows,id']);
        $block->banners()->detach($request->get('attachable_banners', []));
        $block->banners()->attach($request->get('attachable_banners', []));
        Cache::flush();
        return $request->get('attachable_banners', []);
    }

    public function banners(Block $block)
    {
        if (\request()->get('with_paginate', false)) {
            $response = $block->banners()->paginate();
        } else {
            $response = $block->banners()->get();
        }

        foreach ($response as $item) {
            $item->order = $item->pivot->order;
            $item->name = $item->title;
            $item->makeHidden([
                'link', 'title', 'in_new_tab', 'validUntil', 'width', 'height', 'url', 'pivot', 'updated_at',
                'validSince'
            ]);
        }

        return $response;
    }

    public function detachBanners(Block $block, Request $request)
    {
        $request->validate(['detachable_banners' => 'required|array|exists:slideshows,id']);
        $block->banners()->detach($request->get('detachable_banners', []));
        Cache::flush();
        return $request->get('detachable_banners', []);
    }
}
