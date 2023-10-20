<?php


namespace App\Repositories;


use App\Models\Newsletter;
use Illuminate\Support\Arr;

class NewsletterRepo extends AlaaRepo
{
    public static function getModelClass(): string
    {
        return Newsletter::class;
    }

    public static function createNewsletter(array $data)
    {
        return Newsletter::query()
            ->firstOrCreate(['mobile' => Arr::get($data, 'mobile'), 'event_id' => Arr::get($data, 'event_id')], [
                'mobile' => Arr::get($data, 'mobile'),
                'first_name' => Arr::get($data, 'first_name'),
                'last_name' => Arr::get($data, 'last_name'),
                'grade_id' => Arr::get($data, 'grade_id'),
                'major_id' => Arr::get($data, 'major_id'),
                'event_id' => Arr::get($data, 'event_id'),
                'comment' => Arr::get($data, 'comment'),
            ]);
    }

    public static function find(string $mobile)
    {
        return self::initiateQuery()->find($mobile);
    }

    public static function whereIn(array $filters)
    {
        $query = static::initiateQuery();

        foreach ($filters as $col => $values) {
            $query->whereIn($col, $values);
        }

        return $query;
    }
}
