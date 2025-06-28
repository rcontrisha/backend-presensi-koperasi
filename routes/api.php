<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\StatistikPresensiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\PeringatanController;
use App\Models\Peringatan;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [CustomRegisterController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'apiForgotPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/statistik', [StatistikPresensiController::class, 'index']);
    Route::get('/status-presensi', [PresensiController::class, 'statusHariIni']);
    Route::post('/post-presensi', [PresensiController::class, 'presensi']);
    Route::get('/riwayat-presensi', [PresensiController::class, 'riwayatPresensi']);
    Route::get('/izin', [IzinController::class, 'index']);
    Route::get('/izin/{id}', [IzinController::class, 'show']);
    Route::post('/izin', [IzinController::class, 'store']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('peringatan', [PeringatanController::class, 'getSuratPeringatan']);
    Route::put('/user/update-password', [PasswordController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/save-fcm-token', [AuthController::class, 'saveFcmToken']);
});
