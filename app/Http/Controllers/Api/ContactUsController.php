<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactUs as ContactUsResource;
use App\Models\Websitesetting;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    private $setting;

    public function __construct(Websitesetting $setting)
    {
        $this->setting = $setting->setting;
    }

    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return
     */
    public function __invoke(Request $request)
    {
        $contactInfo = collect();
        $contactInfo['branches'] = $this->setting->branches;
        $contactInfo['socialNetwork'] = $this->setting->socialNetwork;

        return (new ContactUsResource($contactInfo))->response();
    }
}
