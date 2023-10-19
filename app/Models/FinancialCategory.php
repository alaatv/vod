<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialCategory extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    public const ONLINE_AUDIO_BOOK_SECOND_GRADE = 1;
    public const _3A = 2;

    protected $table = 'financial_categories';

    protected $fillable = ['name'];
}
