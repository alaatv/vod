<?php

namespace App\Http\Resources;

use App\Models\Contentset;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class BlockableResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        if (($this->blockable instanceof \App\Slideshow)) {
            $type = 'slideshow';
        } elseif (($this->blockable instanceof \App\Content)) {
            $type = 'content';
        } elseif (($this->blockable instanceof Contentset)) {
            $type = 'set';
        } elseif (($this->blockable instanceof \App\Product)) {
            $type = 'product';
        } else {
            return [];
        }

        if (isset($this->blockable->name)) {
            $blockableName = $this->blockable->name;
        } else {
            if (isset($this->blockable->title)) {
                $blockableName = $this->blockable->title;
            } else {
                $blockableName = null;
            }
        }

        if (isset($this->blockable->photo)) {
            $blockablePhoto = $this->blockable->photo;
        } else {
            if (isset($this->blockable->image)) {
                $blockablePhoto = $this->blockable->image;
            } else {
                if (isset($this->blockable->thumbnail)) {
                    $blockablePhoto = $this->blockable->thumbnail;
                } else {
                    $blockablePhoto = null;
                }
            }
        }

        if (isset($this->blockable->redirectUrl)) {
            $blockableUrl = $this->blockable->redirectUrl;
        } else {
            if (isset($this->blockable->link)) {
                $blockableUrl = $this->blockable->link;
            } else {
                $blockableUrl = null;
            }
        }

        return [
            'id' => $this->blockable->id,
            'name' => $blockableName,
            'url' => $blockableUrl,
            'photo' => $blockablePhoto,
            'type' => $type,
            'size' => $this->when($type, function () use ($type) {
                if ($type == 'slideshow') {
                    if (is_null($this->blockable->screensize)) {
                        return null;
                    }
                    return [
                        'id' => $this->blockable->screensize->id,
                        'title' => $this->blockable->screensize->title,
                    ];
                }

                return null;
            })
        ];
    }
}
