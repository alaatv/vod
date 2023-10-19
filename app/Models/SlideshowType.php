<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlideshowType extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const SLIDE_ID = 1;
    public const BANNER_ID = 2;


}
