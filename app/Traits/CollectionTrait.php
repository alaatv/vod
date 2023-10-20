<?php


namespace App\Traits;


use Illuminate\Support\Collection;
use ReflectionClass;

trait CollectionTrait
{
    public function addTypeIndex(): Collection
    {
        $newCollection = new self();
        foreach ($this as $item) {
            $newCollection->push(collect([
                'type' => strtolower(str_replace('Collection', '', (new ReflectionClass($this))->getShortName())),
                'items' => $item
            ]));
        }

        return $newCollection;
    }
}
