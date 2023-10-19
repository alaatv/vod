<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanaProductTransfer extends Model
{
    public const NOT_TRANSFERRED = 1;
    public const TRANSFERRING = 2;
    public const SUCCESSFULLY_TRANSFERRED = 3;
    public const FAILED_TRANSFER = 4;
    use HasFactory;

    protected $fillable = ['dana_course_id', 'product_id', 'status', 'insert_type'];
}
