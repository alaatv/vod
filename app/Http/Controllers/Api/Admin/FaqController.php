<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Faq\EditFaqRequest;
use App\Http\Requests\Faq\StoreFaqRequest;
use App\Http\Resources\FaqResource;
use App\Models\Faq;

class FaqController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.FAQS'), ['only' => ['store', 'edit', 'delete'],]);

    }

    public function store(StoreFaqRequest $request)
    {

        $faq = Faq::create([
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'product_id' => $request->get('product_id', null),
        ]);

        return new FaqResource($faq);
    }

    public function update(EditFaqRequest $request, Faq $faq)
    {

        $faq->update($request->validated());

        return new FaqResource($faq);
    }

    public function delete(Faq $faq)
    {

        $faq->delete();

        return response()->json(['ok']);
    }

    public function index()
    {

        $faqs = Faq::all();

        return FaqResource::collection($faqs);
    }

}
