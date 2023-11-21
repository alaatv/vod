<?php

/*
    These routes are loaded by the RouteServiceProvider within a group which is assigned the "api" middleware group.
*/

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketDepartmentController;
use App\Http\Controllers\Api\TicketMessageController;
use App\Http\Controllers\Api\TicketPriorityController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| V2
|--------------------------------------------------------------------------
*/
Route::prefix('v2')->group(function () {
    // Ticket routes
    Route::group(['prefix' => 'ticket'], function () {
        Route::resource('', '\\'.TicketController::class)->except(['edit']);
        Route::post('{ticket}/sendTicketStatusNotice', [TicketController::class, 'sendTicketStatusChangeNotice']);
        Route::post('{ticket}/assign', [TicketController::class, 'assign']);
        Route::post('{ticket}/rate', [TicketController::class, 'rate']);
    });
    Route::group(['prefix' => 'ticketMessage'], function () {
        Route::post('{ticketMessage}/report', [TicketMessageController::class, 'report']);
    });
    Route::resource('ticketPriority', '\\'.TicketPriorityController::class)->only(['index']);
    Route::resource('ticketDepartment', '\\'.TicketDepartmentController::class, ['as' => 'api'])->only(['index']);
    Route::resource('ticketMessage', '\\'.TicketMessageController::class)->except(['create', 'edit']);
});

