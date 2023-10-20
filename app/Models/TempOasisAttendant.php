<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempOasisAttendant extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'mobile_prefix',
        'mobile_number',
    ];
}
