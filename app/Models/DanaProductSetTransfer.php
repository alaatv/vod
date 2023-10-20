<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanaProductSetTransfer extends Model
{
    public const NOT_TRANSFERRED = 1;
    public const TRANSFERRING = 2;
    public const SUCCESSFULLY_TRANSFERRED = 3;
    public const FAILED_TRANSFER = 4;
    use HasFactory;

    protected $fillable = ['dana_session_id', 'contentset_id', 'status', 'product_id'];
    use HasFactory;
}
