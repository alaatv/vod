<?php

namespace App\Models;


class Sanatisharifmerge extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'videoid',
        'videoTransferred',
        'videoname',
        'videodescrip',
        'videosession',
        'keywords',
        'videolink',
        'videolinkhq',
        'videolink240p',
        'videolinktakhtesefid',
        'videoEnable',
        'thumbnail',
        'pamphletid',
        'pamphletTransferred',
        'pamphletname',
        'pamphletaddress',
        'pamphletdescrip',
        'pamphletsession',
        'isexercise',
        'lessonid',
        'lessonTransferred',
        'lessonname',
        'lessonEnable',
        'depid',
        'departmentTransferred',
        'depname',
        'depyear',
        'departmentlessonid',
        'pic',
        'departmentlessonTransferred',
        'departmentlessonEnable',
        'teacherfirstname',
        'teacherlastname',
        'pageOldAddress',
        'pageNewAddress',
        'educationalcontent_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::Class, 'educationalcontent_id', 'id');
    }
}
