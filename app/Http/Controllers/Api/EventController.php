<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;

class EventController extends Controller
{
    public function show(Event $event)
    {
        return new EventResource($event);
    }

    public function store(EventRequest $request)
    {
        $inputs = $request->validated();
        $event = Event::create($inputs);
        return new EventResource($event);
    }
}
