<?php


namespace App\Repositories;

use App\Models\Websitepage;
use Illuminate\Database\Eloquent\Builder;

class WebsitePageRepo
{
    /**
     * @param  array  $filters
     *
     * @return Builder
     */
    public static function getWebsitePages(array $filters = []): Builder
    {
        $websitePages = Websitepage::query();
        self::filter($filters, $websitePages);
        return $websitePages;
    }

    /**
     * @param  array  $filters
     * @param       $websitePages
     */
    private static function filter(array $filters, Builder $websitePages): void
    {
        foreach ($filters as $key => $filter) {
            if (is_array($filter)) {
                $websitePages->WhereIn($key, $filter);
            } else {
                $websitePages->where($key, $filter);
            }
        }
    }
}
