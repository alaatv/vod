<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hardship extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected const DANA = 'dana';
    protected $fillable = [
        'name',
        'display_name',
        'specifier',
        'specifier_value',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeDanaHardships($query)
    {
        return $query->where('specifier', self::DANA);
    }
}
