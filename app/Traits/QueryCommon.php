<?php namespace App\Traits;

trait QueryCommon
{
    protected function textSearch($text, $columns, &$query)
    {
        if (strlen(preg_replace('/\s+/', '', $text)) <= 0) {

            return $query;
        }
        $words = explode(' ', $text);
        $query = $query->where(function ($q) use ($words, $columns) {
            foreach ($columns as $column) {
                foreach ($words as $word) {
                    $q->orWhere($column, 'like', '%'.$word.'%');
                }
            }
        });


        return $query;
    }
}
