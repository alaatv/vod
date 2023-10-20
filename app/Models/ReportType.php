<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportType extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_display_name',
    ];

    public function scopeId($query, $id)
    {
        return $query->where('id', $id);
    }
}
