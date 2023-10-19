<?php


namespace App\Repositories;


abstract class AlaaRepo
{
    protected static $createdAtKey = 'created_at';

    public static function createdSince(string $since, $query = null)
    {
        if (is_null($query)) {
            $query = self::initiateQuery();
        }

        return $query->where(self::$createdAtKey, '>=', $since);
    }

    public static function initiateQuery()
    {
        $model = static::getModelClass();

        return $model::query();
    }

    abstract public static function getModelClass(): string;

    public static function createdTill(string $till, $query = null)
    {
        if (is_null($query)) {
            $query = self::initiateQuery();
        }

        return $query->where(self::$createdAtKey, '<=', $till);
    }

}
