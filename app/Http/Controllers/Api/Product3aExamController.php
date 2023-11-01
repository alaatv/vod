<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExamProductRelation;
use App\Models\_3aExam;
use App\Models\Product;

class Product3aExamController extends Controller
{
    public function __construct()
    {
    }

    public function detachExam(Product $product, _3aExam $exam)
    {
        $exam->where('product_id', $product->id)->delete();
        return response()->json([
            'message' => "آزمون {$exam->title}  از محصول مورد نظر حذف شد."
        ]);
    }

    public function attachExam(Product $product, CreateExamProductRelation $request)
    {
        $exam = _3aExam::find($request->get('exam_id'));

        if ($exam && $exam->product_id == $product->id) {
            $message = "آزمون {$request->exam_title}قبلا به همین محصول وصل شده است.";
            return response()->json([
                'message' => $message
            ]);
        }

        $product->exams()->create($request->validated());
        $message = "{$request->exam_title}  به محصول مرد نظر متصل شد.";

        return response()->json([
            'message' => $message
        ]);
    }
}