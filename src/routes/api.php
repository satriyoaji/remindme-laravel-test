<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;
use \App\Http\Controllers\Api\ReminderController;

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

Route::post('/session', [AuthController::class, 'login'])->name('generate.token');;

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/session', [AuthController::class, 'refreshToken'])->name('refresh.token');;
    Route::apiResource('reminders', ReminderController::class);
//    Route::get('/user', function (Request $request) {
//        return $request->user();
//    });
//    Route::post('/logout', [AuthController::class, 'logout']);
});
