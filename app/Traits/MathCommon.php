<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-24
 * Time: 13:27
 */

namespace App\Traits;

trait MathCommon
{
    /**
     * make cartesian product
     *
     * @param  $matrix
     * @param  $vertex
     *
     * @return $result
     */
    public function cartesianProduct($matrix, $vertex)
    {
        if (sizeof($matrix) == 0) {
            $result[0][0] = $vertex;

            return $result;
        }
        if (sizeof($matrix) == 1) {
            $result = [];
            $index = 0;
            foreach (current($matrix) as $firstItem) {
                foreach ($vertex as $secondItem) {
                    $result[0][$index] = $firstItem.','.$secondItem;
                    $index++;
                }
            }
            return $result;
        }
        if (sizeof($matrix) < 2) {
            return null;
        }
        $vertex2 = array_pop($matrix);

        return $this->cartesianProduct($this->cartesianProduct($matrix, $vertex2), $vertex);
    }
}
