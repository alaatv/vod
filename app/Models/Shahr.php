<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Shahr extends Model
{
    public const INDEX_PAGE_NAME = 'shahrPage';
    protected $table = 'shahr';

    public static function allDistrictZero()
    {
        return self::query()->where('shahr_type', 0)->get();
    }

    public function ostan()
    {
        return $this->belongsTo(Ostan::class);
    }

    public function entekhabReshteha()
    {
        return $this->belongsToMany(EntekhabReshte::class, 'entekhab_reshte_shahr', 'shahr_id',
            'entekhab_reshte_id')->withPivot('order');
    }
}
