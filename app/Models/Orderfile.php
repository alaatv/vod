<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orderfile extends BaseModel
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'file',
        'description',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getFileAttribute($value)
    {
        return Uploader::privateUrl(config('disks.ORDER_FILE_MINIO'), 36000, $this, $value);
    }
}
