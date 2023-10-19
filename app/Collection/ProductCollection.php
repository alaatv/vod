<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-10-11
 * Time: 15:57
 */

namespace App\Collection;

use App\Models\Product;
use App\Traits\CollectionTrait;
use Illuminate\Database\Eloquent\Collection;

class ProductCollection extends Collection
{
    use CollectionTrait;

    /**
     * If the collection contains parent of products , it keeps the parent and removes it's children
     * Used in ProductController@refreshPrice : In case of calculating the price for a selectable product
     * we need to keep only the parent product and remove all of it's children from the collection(which aren't
     * necessary)
     *
     */
    public function keepOnlyParents(): void
    {
        foreach ($this as $key => $simpleProduct) {
            $this->removeProductDescendants($simpleProduct);
            $parent = $simpleProduct->parents->first();
            if (!isset($parent)) {
                continue;
            }
            if ($this->contains($parent)) {
                $this->forget($key);
            } else {
                //ToDo :  bug: it includes grand parent in collection when the collection includes children in first depth

                // if all children selected and father not selected then select father and remove all children
                /*$children = $parent->children;
                $allChildIsChecked = true;
                foreach ($children as $child) {
                    if (!$this->contains($child))
                        $allChildIsChecked = false;
                }
                if ($allChildIsChecked) {
                    $this->removeProductDescendants($parent);
                    $this->push($parent);
                }*/
            }

        }
    }

    /**
     * Removes products descendants from ProductCollection
     * Used in ProductCollection's keepOnlyParents method in order ro remove
     * a product's all descendants from a collection
     *
     * @param  Product  $product
     */
    public function removeProductDescendants(Product $product): void
    {
        $children = $product->children;

        foreach ($children as $child) {
//          $ck = $this->search($child);  didn't work!!
            $findChildInCollection = $this->where('id', $child->id);
            foreach ($findChildInCollection as $key => $grandChild) {
                $ck = $key;
            }
            if (!isset($ck)) {
                continue;
            }
            $this->forget($ck);
            $grandChildren = $child->children;
            if ($grandChildren->isNotEmpty()) {
                $grandChildren->removeProductDescendants($child);
            }

        }
    }

    public function addSorting()
    {
        $completedAtAscending = $this->sortBy('completed_at')->values();
        $completedAtDescending = $this->sortByDesc('completed_at')->values();

        foreach ($this as $item) {
            $sorting = [];
            $sorting['completed_at_asc'] = $completedAtAscending->where('id', $item->id)->keys()[0];
            $sorting['completed_at_desc'] = $completedAtDescending->where('id', $item->id)->keys()[0];
            $item->sorting = $sorting;
        }
    }

    public function filterEnable()
    {
        return $this->where('enable', 1);
    }

}
