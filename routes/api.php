<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
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
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('checkpages', [ApiController::class, 'checkpages']);
    Route::get('getDashboard', [ApiController::class, 'getDashboard']);
    Route::get('notifications', [ApiController::class, 'notifications']);
    Route::post('updateprofile', [ApiController::class, 'updateprofile']);
    Route::post('optimizecrb', [ApiController::class, 'optimizecrb']);
    Route::post('optimizehustler', [ApiController::class, 'optimizehustler']);
    Route::post('editProfile', [ApiController::class, 'editProfile']);
    Route::post('checkDone', [ApiController::class, 'checkDone']);
});

Route::post('check-refresh', [ApiController::class, 'refresh']);
Route::post('user-signin', [ApiController::class, 'signin']);
Route::post('user-signup', [ApiController::class, 'signup']);
Route::post('payment-callback-stk', [ApiController::class, 'payment_callback_stk']);
