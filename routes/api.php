<?php

use App\Http\Controllers\Api\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Api\Admin\UploadController as AdminUploadController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

// Auth Endpoints
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Admin / Manager GUI Endpoints
    Route::prefix('admin')->group(function () {
        Route::get('/projects', [AdminProjectController::class, 'index']);
        Route::post('/projects', [AdminProjectController::class, 'store']);
        Route::get('/projects/{id}', [AdminProjectController::class, 'show']);
        Route::put('/projects/{id}', [AdminProjectController::class, 'update']);
        Route::delete('/projects/{id}', [AdminProjectController::class, 'destroy']);

        Route::post('/uploads', [AdminUploadController::class, 'store']);
    });
});

// Semua endpoint publik (read-only), kecuali kirim pesan kontak.
Route::get('/portfolio', [PortfolioController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{slug}', [ProjectController::class, 'show']);

// Maksimal 5 pesan per menit per IP.
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');
