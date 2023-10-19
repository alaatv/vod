<?php

namespace App\Models;



class Assignment extends BaseModel
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'deleted_at'=> 'datetime',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'numberOfQuestions',
        'recommendedTime',
        'questionFile',
        'solutionFile',
        'analysisVideoLink',
        'order',
        'enable',
        'assignmentstatus_id',
    ];

    public function assignmentstatus()
    {
        return $this->belongsTo(Assignmentstatus::class);
    }

    public function majors()
    {
        return $this->belongsToMany(Major::class)
            ->withTimestamps();
    }
}
