<?php

use App\Http\Controllers\Api\Admin\StudentLookupController;
use App\Http\Controllers\Api\Admin\StudentSubjectStatusController;
use App\Http\Controllers\Api\Admin\ProspectRegistrationController;
use App\Http\Controllers\Api\Admin\ProspectController as AdminProspectController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProspectController;
use App\Http\Controllers\Api\StudentSubjectController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/prospects', [ProspectController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/student/subjects', [StudentSubjectController::class, 'index']);
    Route::get('/admin/students/by-email', [StudentLookupController::class, 'byEmail']);
    Route::get('/admin/prospects', [AdminProspectController::class, 'index']);
    Route::patch(
        '/admin/students/{studentId}/subjects/{subjectId}',
        [StudentSubjectStatusController::class, 'update']
    );
    Route::post(
        '/admin/prospects/{prospectId}/register-student',
        [ProspectRegistrationController::class, 'registerStudent']
    );
});
