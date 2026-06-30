<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Async List for TFCs
    Route::get('/subjects', [\App\Http\Controllers\Api\SubjectApiController::class, 'index']);
});
