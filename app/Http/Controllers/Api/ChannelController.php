<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Channel as ChannelResource;
use App\Models\Channel;

class ChannelController extends Controller
{
    public function show(Channel $ch)
    {
        return new ChannelResource($ch);
    }
}
