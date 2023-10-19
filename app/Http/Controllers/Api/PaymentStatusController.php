<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Paymentstatus;

class PaymentStatusController extends Controller
{
    public function index()
    {
        return Paymentstatus::collection(\App\Paymentstatus::all());
    }

}
