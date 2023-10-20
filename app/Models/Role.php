<?php

use App\Traits\DateTrait;
use App\Traits\Helper;

class Role extends LaratrustRole
{
    use Helper;
    use DateTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
     * it needs for deleting the role
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
