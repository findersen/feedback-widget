<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketController;

Route::post('/tickets', [TicketController::class, 'store'])
  ->middleware('throttle:ticket-submit');
Route::get('/tickets/statistics', [TicketController::class, 'statistics']);
