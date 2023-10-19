<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoipOperator extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_phone_number',
        'operator_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');

    }

    public function voipCalls()
    {
        return $this->hasMany(Voip::class, 'operator_id', 'operator_id');
    }

}
