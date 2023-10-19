<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;

class FaqController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.FAQS'), ['only' => ['store', 'edit', 'delete'],]);

    }

    public function index()
    {

        $faqs = Faq::all();

        return FaqResource::collection($faqs);
    }

    public function show(Faq $faq)
    {
        return new FaqResource($faq);
    }

}
