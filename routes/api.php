<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsappTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return response()->json(['text' => 'OK']);
});

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::get('customer/{customer:order_id}', [CustomerController::class, 'byOrder']);
    Route::post('customer/message/{customer:order_id}', [CustomerController::class, 'sendMessage']);
    Route::put('customer/message/{customer:order_id}', [CustomerController::class, 'editMessage']);
    Route::get('customers/{date}', [CustomerController::class, 'byDate']);
    Route::get('chart', [DashboardController::class, 'chart']);
    Route::get('card/{date}', [DashboardController::class, 'card']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('default-config', [ConfigController::class, 'default']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::prefix('config')->group(function () {
        Route::get('sending', [ConfigController::class, 'getSending']);
        Route::get('spreadsheet', [ConfigController::class, 'spreadsheet']);
        Route::put('sending', [ConfigController::class, 'sending']);
        Route::put('sync', [ConfigController::class, 'sync']);
        Route::put('link', [ConfigController::class, 'link']);
    });
    Route::apiResource('users', UserController::class);
    Route::apiResource('tokens', WhatsappTokenController::class)->only('index', 'update', 'show');
    Route::apiResource('templates', MessageController::class);
});
