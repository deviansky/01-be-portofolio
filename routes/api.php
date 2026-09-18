<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

// Semua endpoint publik (read-only), kecuali kirim pesan kontak.
Route::get('/portfolio', [PortfolioController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{slug}', [ProjectController::class, 'show']);

// Maksimal 5 pesan per menit per IP.
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');
