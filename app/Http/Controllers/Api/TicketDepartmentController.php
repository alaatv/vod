<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketDepartment as TicketDepartmentResource;
use App\Models\TicketDepartment;
use App\Repositories\TicketRepo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $departments = TicketRepo::getRootDepartmentsSortedByOrder()->enable()->display()->get();
        return (TicketDepartmentResource::collection($departments))->response();
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
     * @param  TicketDepartment  $ticketDepartment
     *
     * @return Response
     */
    public function show(TicketDepartment $ticketDepartment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  TicketDepartment  $ticketDepartment
     *
     * @return Response
     */
    public function update(Request $request, TicketDepartment $ticketDepartment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TicketDepartment  $ticketDepartment
     *
     * @return Response
     */
    public function destroy(TicketDepartment $ticketDepartment)
    {
        //
    }
}
