<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavorableList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favors()
    {
        return $this->hasMany(Favorable::class);
    }
}
