<?php

namespace App\Classes;

interface ISingleton
{
    public static function getInstance(): ISingleton;
}

abstract class Singleton implements ISingleton
{
    private static $_instances = [];

    final public static function getInstance(): ISingleton
    {

        //        $className = get_called_class();
        $className = static::class;
        self::$_instances[$className] = self::$_instances[$className] ?? new static();

        return self::$_instances[$className];
    }
}
