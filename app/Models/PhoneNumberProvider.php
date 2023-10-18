<?php

namespace App\Models;

use App\Traits\ModelsTraits\PhoneNumberProviderTraits\Scopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumberProvider extends Model
{
    use HasFactory;
    use Scopes;

    protected $fillable = ['title'];

    public function phoneNumbers()
    {
        return $this->hasMany(PhoneNumber::class);
    }

    public function provider()
    {
        return $this->belongsTo(SmsProvider::class);
    }

}
