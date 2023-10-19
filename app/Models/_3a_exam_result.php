<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class _3a_exam_result extends Model
{
    use HasFactory;

    protected $table = '3a_exam_results';
    protected $fillable = [
        'user_id', 'exam_id', 'exam_lesson_data', 'exam_ranking_data'
    ];
    protected $casts = [
        'exam_lesson_data' => 'array'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(_3aExam::class);
    }
}
