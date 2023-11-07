<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\SmsUserSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexSmsUserRequest;
use App\Models\SmsUser;

class SmsUserController extends Controller
{


    /**
     * SmsUserController constructor.
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index(IndexSmsUserRequest $request, SmsUserSearch $smsSearch)
    {
        $filters = $request->all();
        $pageName = SmsUser::INDEX_PAGE_NAME;
        $smsSearch->setPageName($pageName);
        if ($request->has('length')) {
            $smsSearch->setNumberOfItemInEachPage($request->get('length'));
        }

        return $smsSearch->get($filters);
    }
}