<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billing';

    public function order()
    {
        return $this->belongsTo(Order::class, 'o_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id');
    }

    public function commission()
    {
        return $this->hasOne(UserCommission::class, 'orderProduct_id', 'op_id');
    }
}
