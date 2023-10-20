<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCommission extends Model
{
    use HasFactory;

    protected $table = 'userCommission';

    protected $fillable = [
        'user_id',
        'orderProduct_id',
        'payment_transaction_id',
        'transaction_id',
        'commision',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(Orderproduct::class, 'orderProduct_id');
    }
}
