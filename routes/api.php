<?php

use App\Http\Controllers\API\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/bookings/reserve', [BookingController::class, 'reserve']);

Route::get('/list_venues',[BookingController::class, 'list_venues']);

Route::get('/categorize_venues',[BookingController::class, 'categorize_venues']);
