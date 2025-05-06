<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatistikPresensiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomRegisterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [CustomRegisterController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/statistik', [StatistikPresensiController::class, 'index']);
});
