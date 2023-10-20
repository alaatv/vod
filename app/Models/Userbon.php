<?php

namespace App\Models;

class Userbon extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'bon_id',
        'user_id',
        'totalNumber',
        'usedNumber',
        'validSince',
        'validUntil',
        'orderproduct_id',
        'userbonstatus_id',
    ];

    public function userbonstatus()
    {
        return $this->belongsTo(Userbonstatus::class);
    }

    public function bon()
    {
        return $this->belongsTo(Bon::Class);
    }

    public function user()
    {
        return $this->belongsTo(User::Class);
    }

    public function orderproducts()
    {
        return $this->belongsToMany(Orderproduct::Class);
    }

    public function orderproduct()
    {
        return $this->belongsTo(Orderproduct::Class);
    }

    public function void()
    {
        $remainBonNumber = $this->totalNumber - $this->usedNumber;
        $this->usedNumber = $this->totalNumber;
        $this->userbonstatus_id = config('constants.USERBON_STATUS_USED');
        $this->update();

        return $remainBonNumber;
    }
}
