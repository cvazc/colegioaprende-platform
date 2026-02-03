<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentSubjectController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/student/subjects', [StudentSubjectController::class, 'index']);
});
