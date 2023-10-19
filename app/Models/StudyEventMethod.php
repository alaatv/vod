<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyEventMethod extends Model
{
    use HasFactory;

    protected $table = 'studyevent_methods';
    protected $fillable = [
        'title',
        'display_name',
    ];

    public function studyEvents()
    {
        return $this->hasMany(Studyevent::class, 'method_id');
    }
}
