<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ordermanagercomment extends BaseModel
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'comment',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
