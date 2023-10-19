<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Firebasetoken extends Model
{
    /**      * The attributes that should be mutated to dates.        */
    protected $casts = [
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'deleted_at'=> 'datetime',
    ];

    protected $fillable = [
        'token',
        'refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
