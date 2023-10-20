<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderable_type',
        'orderable_id',
        'order_id',
        'third_party_order_id',
    ];

    public function orderable()
    {
        return $this->morphTo();
    }

    public function orderProducts()
    {
        return $this->hasMany(ThirdPartyOrderProduct::class);
    }
}
