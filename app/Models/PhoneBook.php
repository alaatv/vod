<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhoneBook extends BaseModel
{
    use DateTrait;
    use HasFactory;

    protected $fillable = ['title'];

    public function phoneNumbers()
    {
        return $this->belongsToMany(PhoneNumber::class, 'phone_book_number');
    }
}
