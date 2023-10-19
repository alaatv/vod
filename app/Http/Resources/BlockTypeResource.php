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
class BlockTypeResource extends AlaaJsonResource
{
    public function toArray($resource)
    {
        if (!($this->resource instanceof \App\BlockType)) {
            return [];
        }

        return [
            'id' => $this->resource,
            'name' => $this->when($this->name, $this->name),
            'display_name' => $this->when($this->display_name, $this->display_name),
            'description' => $this->when($this->description, $this->description),
        ];
    }
}
