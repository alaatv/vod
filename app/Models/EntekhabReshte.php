<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntekhabReshte extends Model
{
    use HasFactory;

    protected $table = 'entekhab_reshte';
    protected $fillable = [
        'user_id',
        'file',
        'comment',
        'majors',
    ];
    protected $casts = [
        'majors' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shahrha()
    {
        return $this->belongsToMany(
            Shahr::class,
            'entekhab_reshte_shahr',
            'entekhab_reshte_id',
            'shahr_id'
        )->withPivot('order');
    }

    public function universityTypes()
    {
        return $this->belongsToMany(
            UniversityType::class,
            'entekhab_reshte_university_type',
            'entekhab_reshte_id',
            'university_type_id'
        );
    }

    public function getFilettribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Uploader::url(config('disks.ENTEKHAB_RESHTE'), $value);
    }
}
