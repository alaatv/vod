<?php

namespace App\Http\Controllers\Api\BonyadEhsan;

use App\Http\Controllers\Controller;
use App\Http\Resources\Abrisham\AbrishamLessonResource;
use App\Models\Major;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function abrishamLessons(Request $request)
    {
        $isPro = $request->get('isPro', 0);
        $userMajorCategory = -1;
        if (
            isset($request->user()->major_id) &&
            in_array($request->user()->major_id, [Major::RIYAZI, Major::TAJROBI])
        ) {
            $userMajorCategory = $request->user()->major_id;
        }

        switch ($isPro) {
            case 0 :
                $abrishamCategory = Product::ABRISHAM_PRODUCTS_CATEGORY;
                $abrishamLessonsInfo = Product::ALL_ABRISHAM_PRODUCTS;
                break;
            case 1:
                $abrishamCategory = Product::ABRISHAM_PRO_PRODUCTS_CATEGORY;
                $abrishamLessonsInfo = Product::ALL_ABRISHAM_PRO_PRODUCTS;
                break;
        }
        $abrishamCategory['foriat-ensani'] = [
            'user_major_category' => Major::ENSANI, 'products' => array_keys(Product::ALL_FORIAT_ENSANI_PRODUCTS),
            'title' => 'فوریت انسانی'
        ];
        $abrishamLessonsInfo = $abrishamLessonsInfo + Product::ALL_FORIAT_ENSANI_PRODUCTS;
        $categories = [];
        foreach ($abrishamCategory as $category) {
            $lessons = [];
            $counter = 0;
            foreach ($category['products'] as $productId) {
                $lessonInfo = Arr::get($abrishamLessonsInfo, $productId);
                $lessons[] = [
                    'id' => $productId,
                    'title' => Arr::get($lessonInfo, 'lesson_name'),
                    'color' => Arr::get($lessonInfo, 'color'),
                    'selected' => $counter++ == 0 && $userMajorCategory == $category['user_major_category'],
                ];
            }
            $categories[] = [
                'title' => $category['title'],
                'lessons' => $lessons,
            ];
        }

        return AbrishamLessonResource::collection($categories);
    }
}
