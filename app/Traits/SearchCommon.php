<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/10/2018
 * Time: 12:07 PM
 */

namespace App\Traits;

use Illuminate\Support\Facades\View;

trait SearchCommon
{
    /**
     * Makes partial view for search query
     *
     * @param $query
     *
     * @return string
     */
    private function getPartialSearchFromIds($query, $layout)
    {
        $partialSearch = View::make($layout, ['items' => $query])
            ->render();

        return $partialSearch;
    }

    private function validateLengthPaginate($length): bool
    {

        return $length && intval($length) > 0 && intval($length) <= 100;

    }

}
