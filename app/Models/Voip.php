<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voip extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'caller_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo(VoipOperator::class, 'operator_id', 'operator_id');
    }
}
