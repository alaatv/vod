<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyOrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'third_party_order_id',
        'order_product_id',
        'third_party_product_item_id',
    ];

    public function order()
    {
        return $this->belongsTo(ThirdPartyOrder::class);
    }
}
