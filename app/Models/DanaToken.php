<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanaToken extends Model
{
    use HasFactory;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
    ];

    protected $fillable = ['access_token', 'refresh_token', 'expires_at'];
    use HasFactory;
}
