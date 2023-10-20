<?php

namespace App\Traits;


use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait CommentTrait
 * @package App\Traits
 * @property-read MorphMany $comments
 */
trait CommentTrait
{
    /**
     * Get all of the Models' comments.
     *
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
