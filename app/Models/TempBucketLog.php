<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempBucketLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat',
        'file_url',
        'error_detail',
        'status_of_retry'
    ];
}
