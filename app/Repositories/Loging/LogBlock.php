<?php

namespace App\Repositories\Loging;

trait LogBlock
{
    public $changedOrders = [];
    private $addOrRemovedItems = [];

    public function logChanges(
        $block,
        array $blockable = [],
        array $current_items = [],
        string $type = '',
        $lastOrder = [],
        array $newOrder = [],
        string $relation = ''
    ): void {
        $this->addChangeInRelations($blockable, $current_items, $type);
        $this->addChangeInOrders($lastOrder, $newOrder, $relation);
        $changes = array_merge($this->addOrRemovedItems, $this->changedOrders);
        if ($changes) {
            ActivityLogRepo::logBlockChanges($block, $changes);
        }
    }

    protected function addChangeInRelations(array $blockable, array $current_items, string $type): void
    {
        $added = array_diff($blockable, $current_items);
        $deleted = array_diff($current_items, $blockable);
        if ($added) {
            $this->addOrRemovedItems['added'][$type] = $added;
        }
        if ($deleted) {
            $this->addOrRemovedItems['deleted'][$type] = $deleted;
        }
    }

    public function addChangeInOrders($lastOrder, array $newOrder, string $relation): void
    {
        foreach ($lastOrder as $item) {
            foreach ($newOrder as $key => $order) {
                if (($item->id == $key) && ($order != $item->pivot->order)) {
                    $this->changedOrders['changed orders'][$relation][] = ["id-$key: {$item->pivot->order}->$order"];
                }
            }
        }
    }
}
