<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexGatewayRequest;
use App\Http\Resources\GatewayResource;
use App\Repositories\TransactionGatewayRepo;

class GatewayController extends Controller
{
    public function index(IndexGatewayRequest $indexGatewayRequest)
    {

        $gateways = TransactionGatewayRepo::findEnabledGateways()->orderBy('order');

        if ($indexGatewayRequest->has('gateway')) {
            $gateways->where('name', $indexGatewayRequest->get('gateway'));
        }

        return GatewayResource::collection($gateways->get());
    }
}
