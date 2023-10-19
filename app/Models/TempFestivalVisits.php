<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempFestivalVisits extends Model
{
    use HasFactory;

    protected $fillable = ['mobile', 'visit_times'];
}
