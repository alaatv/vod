<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyVastSetRequest;
use App\Http\Requests\StoreVastSetRequest;
use App\Models\Contentset;
use App\Models\Vast;
use Illuminate\Support\Facades\Cache;


class VastSetController extends Controller
{
    public function index(Vast $vast)
    {
        return view('vast.sets', [
            'vast' => $vast,
            'sets' => $vast->sets,
        ]);
    }

    public function store(StoreVastSetRequest $request, Vast $vast)
    {
        $ids = $request->input('ids');

        $this->flushVastSetCache($ids);
        $vast->sets()->attach($ids, $request->only(['valid_since', 'valid_until']));

        return back()->with('success', 'عملیات افزودن دسته محتوا به وست با موفقیت انجام شد.');
    }

    public function destroy(DestroyVastSetRequest $request, Vast $vast)
    {
        $ids = $request->input('ids');

        $this->flushVastSetCache($ids);
        $vast->sets()->detach($ids);

        return back()->with('success', 'عملیات حذف دسته محتوا از وست با موفقیت انجام شد.');
    }

    public function flushVastSetCache(array $ids)
    {
        foreach ($ids as $id) {
            foreach (Contentset::find($id)->contents as $content) {
                Cache::tags(["content_{$content->id}_vast"])->flush();
            }
        }
    }
}