<?php

namespace App\Http\Resources;

use App\Models\Block;


/**
 * Class Block
 *
 * @mixin Block
 *
 * @property mixed notRedirectedContents
 * @property mixed sets
 */
class BlockType extends AlaaJsonResource
{
    public function toArray($resource)
    {
        if (!($this->resource instanceof Block) and !(array_key_exists($this->resource, Block::TYPES))) {
            return [
            ];
        }

        return [
            'id' => $this->resource,
            'type' => Block::TYPES[$this->resource],
        ];
    }
}
