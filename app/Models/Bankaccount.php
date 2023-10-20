<?php

namespace App\Models;

use App\Traits\logger;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Bankaccount extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    use HasFactory;
    use logger;

    public const LOG_ATTRIBUTES = [
        'user_id'
    ];
    protected $fillable = [
        'user_id',
        'bank_id',
        'accountNumber',
        'cardNumber',
        'preShabaNumber',
        'shabaNumber'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
