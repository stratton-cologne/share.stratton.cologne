<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SharedFileController;

Route::post('/files', [SharedFileController::class, 'store']);
Route::get('/files/{token}', [SharedFileController::class, 'show']);
Route::get('/files/{token}/download', [SharedFileController::class, 'download']);
Route::get('/batches/{token}', [SharedFileController::class, 'showBatch']);
Route::get('/batches/{token}/download', [SharedFileController::class, 'downloadBatch']);
