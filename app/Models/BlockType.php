<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockType extends BaseModel
{
    public const TYPE_HOME_ID = 1;
    public const TYPE_SHOP_ID = 2;
    public const TYPE_PRODUCT_ID = 3;
    public const TYPE_4TH_ID = 4;
    public const TYPE_5TH_ID = 5;
    public const TYPE_6TH_ID = 6;
    public const TYPE_7TH_ID = 7;

    protected $fillable = ['name', 'display_name'];

    /**
     * @return HasMany
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'type');
    }
}
