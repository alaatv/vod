<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCodeUser extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code_id',
        'subject_id',
        'subject_type',
    ];
    protected $table = 'referral_code_user';

    /**
     * Get all of the users that used the code.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get referralCode that used by users.
     */
    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class, 'code_id', 'id');
    }
}
