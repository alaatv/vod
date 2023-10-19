<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralRequest extends Model
{
    use HasFactory;

    protected $table = 'referralRequests';
    protected $fillable = [
        'owner_id',
        'discounttype_id',
        'discount',
        'numberOfCodes',
        'usageLimit',
        'default_commission',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function discountType()
    {
        return $this->belongsTo(Discounttype::class, 'discounttype_id');
    }

    public function referralRequest()
    {
        return $this->belongsTo(ReferralRequest::class, 'referralRequest_id');
    }

    public function referralCodes()
    {
        return $this->hasMany(ReferralCode::class, 'referralRequest_id');
    }

}
