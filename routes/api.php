<?php

use App\Http\Controllers\Api\KoreksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Kirim koreksi dari aplikasi (publik, anonim) — dibatasi rate-limit.
Route::post('/koreksi', [KoreksiController::class, 'store'])
    ->middleware('throttle:30,1');

// Dibaca mesin terjemah (FastAPI) untuk injeksi RAG.
Route::get('/koreksi/disetujui', [KoreksiController::class, 'disetujui']);
