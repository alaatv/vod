<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository
{
    public static function getKounkurEvents()
    {
        return Event::where('name', 'like', 'konkur%');
    }

    public static function getEventByName(string $name)
    {
        return Event::where('name', $name);
    }
}
