<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Major extends BaseModel
{
    public const RIYAZI = 1;
    public const TAJROBI = 2;
    public const ENSANI = 3;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'majortype_id',
        'description',
        'enable',
        'order',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function assignments()
    {
        return $this->belongsToMany(Assignment::class);
    }

    public function consultations()
    {
        return $this->belongsToMany(Consultation::class);
    }

    public function majortype()
    {
        return $this->belongsTo(Majortype::class);
    }

    public function parents()
    {
        return $this->belongsToMany(Major::class, 'major_major', 'major2_id', 'major1_id')
            ->withPivot('relationtype_id',
                'majorCode')
            ->join('majorinterrelationtypes', 'relationtype_id',
                'majorinterrelationtypes.id')//            ->select('major1_id AS id', 'majorinterrelationtypes.name AS pivot_relationName' , 'majorinterrelationtypes.displayName AS pivot_relationDisplayName')
            ->where('relationtype_id', 1);
    }

    public function children()
    {
        return $this->belongsToMany(Major::class, 'major_major', 'major1_id', 'major2_id')
            ->withPivot('relationtype_id',
                'majorCode')
            ->join('majorinterrelationtypes', 'relationtype_id', 'majorinterrelationtypes.id')
            ->where('relationtype_id', 1);
    }

    public function accessibles()
    {
        return $this->belongsToMany(Major::class, 'major_major', 'major1_id', 'major2_id')
            ->withPivot('relationtype_id',
                'majorCode')
            ->join('majorinterrelationtypes', 'relationtype_id', 'majorinterrelationtypes.id')
            ->where('relationtype_id', 2);
    }

    public function contents()
    {
        return $this->belongsToMany(Content::class);
    }

    public function newsLetters()
    {
        return $this->hasMany(Newsletter::class);
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }

    /**
     * Scope a query to only include enable Blocks.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeEnable($query)
    {
        return $query->where('enable', 1);
    }
}
