<?php

namespace App\Models;


class Faq extends BaseModel
{
    protected $fillable = [
        'product_id',
        'title',
        'body',
    ];

    public function product()
    {
        return $this->belongsTo(Product::Class);
    }
}
