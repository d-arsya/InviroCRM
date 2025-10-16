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
    Route::get('chart', [DashboardController::class, 'chart']);
    Route::get('card/{date}', [DashboardController::class, 'card']);
    Route::controller(CustomerController::class)->group(function () {
        Route::get('customer/{customer:order_id}', 'byOrder');
        Route::post('customer/message/{customer:order_id}', 'sendMessage');
        Route::put('customer/message/{customer:order_id}', 'editMessage');
        Route::get('customers/messages', 'messages');
        Route::get('customers/{date}', 'byDate');
    });
    Route::controller(AuthController::class)->group(function () {
        Route::get('me', 'me');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
    });
    Route::controller(ConfigController::class)->group(function () {
        Route::get('default-config', 'default');
        Route::prefix('config')->group(function () {
            Route::get('sending', 'getSending');
            Route::get('spreadsheet', 'spreadsheet');
            Route::put('sending', 'sending');
            Route::put('sync', 'sync');
            Route::put('link', 'link');
        });
    });
    Route::apiResource('users', UserController::class);
    Route::apiResource('tokens', WhatsappTokenController::class)->only('index', 'update', 'show');
    Route::apiResource('templates', MessageController::class);
});
