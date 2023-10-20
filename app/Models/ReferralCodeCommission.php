<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCodeCommission extends Model
{
    use HasFactory;

    protected $table = 'referralCodeCommissions';

    protected $fillable = [
        'referralRequest_id',
        'referralable_id',
        'referralable_type',
        'commission'
    ];

    public function referralRequest()
    {
        return $this->belongsTo(ReferralRequest::class, 'referralRequest_id');
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
