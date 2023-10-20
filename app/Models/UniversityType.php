<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name'
    ];

    public function entekhabReshteha()
    {
        return $this->belongsToMany(
            EntekhabReshte::class,
            'entekhab_reshte_university_type',
            'university_type_id',
            'entekhab_reshte_id'
        );
    }
}
