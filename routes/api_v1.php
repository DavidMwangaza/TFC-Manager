<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChapterApiController;
use App\Http\Controllers\Api\ChapterVersionApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/subjects/{subject}/chapters', [ChapterApiController::class, 'index']);
    Route::post('/subjects/{subject}/chapters', [ChapterApiController::class, 'store']);

    Route::post('/chapters/{chapter}/versions', [ChapterVersionApiController::class, 'store']);
    Route::get('/chapters/{chapter}/versions', [ChapterVersionApiController::class, 'index']);
});
