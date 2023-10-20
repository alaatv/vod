<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ContentOfPlanType extends Model
{

    public const TYPE_LESSON_VIDEO = 4;

    protected $table = 'educationalcontent_of_plan_types';

    protected $fillable = [
        'title',
        'display_name',
    ];


}
