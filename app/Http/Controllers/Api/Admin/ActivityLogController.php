<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\ActivityLogSearch;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ActivityLogResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * ActivityLogController constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param  Request  $request
     * @param  ActivityLogSearch  $activityLogSearch
     * @return ResourceCollection
     */
    public function index(Request $request, ActivityLogSearch $activityLogSearch)
    {
        $filters = $request->all();
        $pageName = Activity::INDEX_PAGE_NAME;
        $activityLogSearch->setPageName($pageName);
        if ($request->all()) {
            $activityLogSearch->setNumberOfItemInEachPage($request->get('length'));
        }

        $results = $activityLogSearch->get($filters);

        return ActivityLogResource::collection($results);
    }
}
