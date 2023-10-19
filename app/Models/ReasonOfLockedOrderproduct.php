<?php

namespace App\Models;


class ReasonOfLockedOrderproduct extends BaseModel
{
    protected $table = 'reasonsOfLockedOrderproducts';

    protected $fillable = [
        'text',
    ];

    public function orderproducts()
    {
        return $this->belongsToMany(Orderproduct::class, 'lockedOrderproducts', 'reason_lock_id', 'orderproduct_id');
    }
}
