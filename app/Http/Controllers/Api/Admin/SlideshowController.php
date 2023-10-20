<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\SlideshowSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateSlideShowRequest;
use App\Http\Requests\Admin\EditSlideShowRequest;
use App\Http\Requests\Admin\RestoreSlideShowRequest;
use App\Http\Resources\Admin\SlideshowResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Slideshow;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlideshowController extends Controller
{
    private $numberOfItemInEachPage = 20;

    /**
     * SlideshowController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_SLIDESHOW_ACCESS'), ['only' => 'index']);
    }

    /**
     * @param  Request  $request
     * @param  SlideshowSearch  $slideshowSearch
     * @return ResourceCollection
     */
    public function index(Request $request, SlideshowSearch $slideshowSearch)
    {
        $filters = $request->all();
        $pageName = Slideshow::INDEX_PAGE_NAME;
        $slideshowSearch->setPageName($pageName);
        if ($request->has('length')) {
            // TODO: This is temporary.
            $slideshowSearch->setNumberOfItemInEachPage($request->get('length'));
            $slideshowSearch->setNumberOfItemInEachPage($this->numberOfItemInEachPage);
        }
        $slideshowSearch->setNumberOfItemInEachPage($this->numberOfItemInEachPage);

        $results = $slideshowSearch->get($filters);

        return SlideshowResource::collection($results);
    }

    /**
     * Display a listing of the resource in trash bin.
     *
     * @return JsonResponse
     */
    public function indexTrashBin()
    {
        return \App\Http\Resources\Slideshow::collection(
            Slideshow::onlyTrashed()->orderByDesc('deleted_at')->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateSlideShowRequest  $request
     * @return JsonResponse
     */
    public function store(CreateSlideShowRequest $request)
    {
        $newSlideShow = Slideshow::create($request->validated());
        return new \App\Http\Resources\Slideshow($newSlideShow->fresh());
    }

    /**
     * Display the specified resource.
     *
     * @param  Slideshow  $slideshow
     * @return JsonResponse
     */
    public function show(Slideshow $slideshow)
    {
        return new \App\Http\Resources\Slideshow($slideshow);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditSlideShowRequest  $request
     * @param  Slideshow  $slideshow
     * @return JsonResponse
     */
    public function update(EditSlideShowRequest $request, Slideshow $slideshow)
    {
        $slideshow->update($request->validated());
        return new \App\Http\Resources\Slideshow($slideshow);
    }

    /**
     * soft delete the specified resource from storage.
     *
     * @param  Slideshow  $slideshow
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Slideshow $slideshow)
    {
        $slideshow->delete();
        return new \App\Http\Resources\Slideshow($slideshow);
    }

    /**
     * restore the specified resource from storage.
     *
     * @param  RestoreSlideShowRequest  $request
     * @return JsonResponse
     */
    public function restore(RestoreSlideShowRequest $request)
    {
        $slideshow = Slideshow::withTrashed()->firstWhere('id', $request->get('slideshow_id'));
        $slideshow->restore();
        return new \App\Http\Resources\Slideshow($slideshow);
    }
}
