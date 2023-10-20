<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LatLng extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'map_detail_id',
        'lat',
        'lng'
    ];

    public function mapDetail()
    {
        return $this->belongsTo(MapDetail::class, 'map_detail_id');
    }
}
