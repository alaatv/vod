<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EwanoUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ewano_user_id',
        'refresh_token',
        'ewano_order_id',
        'alaa_order_id',
    ];

    public function alaaUser()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->morphMany(ThirdPartyOrder::class, 'orderable');
    }
}
