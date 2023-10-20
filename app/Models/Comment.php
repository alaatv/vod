<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'commentable_id',
        'commentable_type',
        'comment',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the parent commentable model (Content, ContentSet, Product and ...)
     *
     * @return MorphTo
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeSearch($query, $keywords)
    {
        $keywords = explode(' ', $keywords);
        foreach ($keywords as $keyword) {
            $query->where('comment', 'LIKE', '%'.$keyword.'%');
        }
        return $query;
    }
}
