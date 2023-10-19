<?php


namespace App\Traits;




use App\Models\Content;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

trait DraftTrait
{

    private function setModel($model, $id)
    {
        switch ($model) {
            case Product::class:
                return Product::find($id);
            case Content::class:
                return Content::find($id);
            default:
                return myAbort(Response::HTTP_UNPROCESSABLE_ENTITY, 'This model not supported');
        }
    }

}
