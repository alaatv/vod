<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blockable extends BaseModel
{
    use HasFactory;

    public function blockable()
    {
        return $this->morphTo();
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id', 'id')->orderBy('order', 'asc');
    }
}
