<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\Product;

class SeoService
{
    private string $type;
    private ?BaseModel $model = null;

    public function setType($type): static
    {
        $this->type = $type;
        return $this;
    }

    public function setModel($id): static
    {
        $model = match ($this->type) {
            'content' => Content::class,
            'product' => Product::class,
            'content-set' => Contentset::class,
            default => null
        };
        if (isset($model)) {
            $this->model = $model::find($id);
        }
        return $this;
    }

    public function getSeo()
    {
        return isset($this->model) ? $this->model->getMetaTags() : null;
    }
}
