<?php

use Illuminate\Support\Facades\Route;
use Jdlx\Http\Controllers\Api\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/auth/csfr', [AuthController::class, "csfr"]);
Route::post('/auth/token', [AuthController::class, "token"]);
Route::post('/auth/login',  [AuthController::class, "login"]);
Route::post('/auth/logout',  [AuthController::class, "logout"]);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user', '\App\Http\Controllers\Api\UserController');
});

