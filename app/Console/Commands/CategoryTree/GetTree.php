<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 2/10/2019
 * Time: 10:58 AM
 */

namespace App\Console\Commands\CategoryTree;

abstract class GetTree
{
    abstract public function getLastUpdatedByLernito(): array;

    public function getTotalTree()
    {
        $Riazi = new Riazi();
        $Tajrobi = new Tajrobi();
        $Ensani = new Ensani();
        $totalTree = array_merge($Riazi->getTree(), $Tajrobi->getTree(), $Ensani->getTree());

        return $totalTree;
    }

    abstract public function getTree(): array;

    protected function treeToLernitoJson(array $tree)
    {
        $return = [];
        array_walk_recursive($tree, function ($value, $key) use (&$return) {
            $return[] = [
                'label' => $value['name'],
                'children' => $this->getChildIds($value['children']),
            ];
        });

        return $return;

//        $lernitoJsonTree = [];
//        $counter = 0;
//        foreach ($tree as $key=>$item) {
//            if (isset($item['children']) && count($item['children'])>0) {
//                $lernitoJsonTreeItem = [
//                    'label' => $item['name'],
//                    'level' => ($this->level++),
//                    'children' => $item['children']
//                ];
//
//                $this->treeToLernitoJson($item['children']);
//            } else {
//
//            }
//        }
    }

    protected function getChildIds(array $children)
    {
        return array_column($children, 'id');
    }

    protected function convertLernito(array $lernitoTree)
    {
        $convertedStyle = [];

        return $resultChildren = $this->getChildrenFromLernitoStyle($lernitoTree, $lernitoTree[0]['children']);
//        foreach ($lernitoTree as $key=>$value) {
//            $resultChildren = $this->getChildrenFromLernitoStyle($lernitoTree, $value['children']);
//            $value['children'] = $resultChildren;
//            $convertedStyle[] = $value;
////            if (!$this->searchInConvertedStyle($convertedStyle, $value['_id'])) {
////                $convertedStyle[] = $value;
////            }
//        }
    }

    private function getChildrenFromLernitoStyle(array &$lernitoTree, array $children)
    {
        $resultChildren = [];
        foreach ($children as $value) {
            $resultChildren[] = $lernitoTree[$value];
            unset($lernitoTree[$value]);
        }
        foreach ($resultChildren as $key => $value) {
            if (!isset($value['children'])) {
                continue;
            }
            $resultChildren[$key]['children'] = $this->getChildrenFromLernitoStyle($lernitoTree, $value['children']);
        }

        return $resultChildren;
    }

    protected function compareWithLernito(array $lernitoTree, array $targetTree)
    {
        $diff = $this->compareSingleLevel($lernitoTree, $targetTree);

        return $diff;
    }

    private function compareSingleLevel(array $lernito, array $target)
    {
        $localKeyChain = [];
        $diff = [];
        foreach ($lernito as $lernitoKey => $lernitoValue) {
            $hasVal = false;
            $localKeyChain[] = $lernitoValue['label'];
            foreach ($target as $targetKey => $targetValue) {
                if ($targetValue['name'] != $lernitoValue['label']) {
                    continue;
                }
                $hasVal = true;
                if (isset($lernitoValue['children']) && isset($targetValue['children'])) {
                    $newDiff = $this->compareSingleLevel($lernitoValue['children'], $targetValue['children']);
                    if (count($newDiff) > 0) {
                        if (isset($newDiff['keyChain'])) {
                            $localKeyChain[] = $newDiff['keyChain'];
                        }
                        $diff[] = [
                            'diff' => $newDiff,
                            'lernitoNode' => $lernitoValue,
                            'alaaNode' => $targetValue,
                        ];
                    }
                } else {
                    if (isset($lernitoValue['children'])) {
                        $diff[] = [
                            'diff' => $lernitoValue['children'],
                            'lernitoNode' => $lernitoValue,
                            'alaaNode' => $targetValue,
                        ];
                    }
                }
            }
            if (!$hasVal) {
                $diff[] = [
                    'lernitoNode' => $lernitoValue,
                    'keyChain' => 1,
                ];
            }
            $localKeyChain = [];
        }

        return $diff;
    }
}
