<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductRenewal extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'orderProductRenewals';

    protected $fillable = [
        'orderproduct_id',
        'expired_at',
        'accepted_at',
        'accepted_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    //region relation
    public function orderproduct()
    {
        return $this->belongsTo(Orderproduct::Class);
    }
    //endregion

    //region scope
    public function scopeNotAccepted($query)
    {
        return $query->whereNull(['accepted_at', 'accepted_by']);
    }
    //endregion


}
