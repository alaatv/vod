<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonyadEhsanConsultant extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id', 'student_register_limit', 'student_register_number'];

    // relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // business logics
    public function increaseRegistrationNumber(int $amount = 1)
    {
        $this->student_register_number += $amount;
        $this->save();
    }
}
