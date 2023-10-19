<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    /**      * The attributes that should be mutated to dates.        */
    protected $casts = [
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'deleted_at'=> 'datetime',
        'since'=> 'datetime',
        'till'=> 'datetime',
    ];

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'product_id',
        'registerer_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::Class);
    }

    public function registerer()
    {
        return $this->belongsTo(User::Class);
    }

    public function product()
    {
        return $this->belongsTo(Product::Class);
    }
}
