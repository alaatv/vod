<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketPriority as TicketPriorityResource;
use App\Models\TicketPriority;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketPriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return (TicketPriorityResource::collection(TicketPriority::all()))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  TicketPriority  $ticketPriority
     *
     * @return Response
     */
    public function show(TicketPriority $ticketPriority)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  TicketPriority  $ticketPriority
     *
     * @return Response
     */
    public function update(Request $request, TicketPriority $ticketPriority)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TicketPriority  $ticketPriority
     *
     * @return Response
     */
    public function destroy(TicketPriority $ticketPriority)
    {
        //
    }
}
