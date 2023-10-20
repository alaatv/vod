<?php

namespace App\Models;


class EducationalContentPlan extends BaseModel
{

    protected $table = 'educationalcontent_plan';

    protected $fillable = [
        'plan_id',
        'content_id',
        'title',
        'type_id',
    ];

}
