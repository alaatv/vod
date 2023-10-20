<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentsStatus extends Model
{
    use HasFactory;

    public const CONTENT_STATUS_PENDING = 1;
    public const CONTENT_STATUS_DRAFT = 2;
    public const CONTENT_STATUS_COMPLETED = 3;
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    public function contents()
    {
        return $this->hasMany(Content::class, 'content_status_id');
    }
}
