<?php

namespace App\Models;


class Conductor extends BaseModel
{
    protected $table = 'liveconductors';

    /**      * The attributes that should be mutated to dates.        */
    protected $casts = [
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'deleted_at'=> 'datetime',
    ];

    protected $fillable = [
        'product_id',
        'title',
        'live_link',
        'description',
        'poster',
        'date',
        'start_time',
        'finish_time',
        'class_name',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'live_conductor_user',
            'live_conductor_id',
            'user_id'
        )->withTimestamps();
    }
}
