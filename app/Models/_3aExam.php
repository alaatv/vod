<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class _3aExam extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = '3a_exams';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'title',
        'product_id',
    ];

    // relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeProductId($query, int $id)
    {
        return $query->where('product_id', $id);
    }
}
