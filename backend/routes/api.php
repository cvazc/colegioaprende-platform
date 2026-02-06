<?php

use App\Http\Controllers\Api\Admin\StudentLookupController;
use App\Http\Controllers\Api\Admin\StudentSubjectStatusController;
use App\Http\Controllers\Api\Admin\ProspectRegistrationController;
use App\Http\Controllers\Api\Admin\ProspectController as AdminProspectController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\CashPaymentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentItemController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\ProspectController;
use App\Http\Controllers\Api\StudentCreditController;
use App\Http\Controllers\Api\StudentPaymentController;
use App\Http\Controllers\Api\StudentSubjectController;
use App\Http\Controllers\Api\StudentSubjectUnlockController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/prospects', [ProspectController::class, 'store']);
Route::get('/payment-items', [PaymentItemController::class, 'index']);
Route::post('/prospects/{prospectId}/payments', [PaymentController::class, 'store']);
Route::post('/payments/webhooks/{provider}', [PaymentWebhookController::class, 'handle']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/student/subjects', [StudentSubjectController::class, 'index']);
    Route::post('/student/subjects/{subjectId}/unlock', [StudentSubjectUnlockController::class, 'store']);
    Route::get('/student/payments', [StudentPaymentController::class, 'index']);
    Route::get('/student/credits', [StudentCreditController::class, 'show']);
    Route::get('/admin/students/by-email', [StudentLookupController::class, 'byEmail']);
    Route::get('/admin/prospects', [AdminProspectController::class, 'index']);
    Route::post('/admin/prospects/{prospectId}/payments/cash', [CashPaymentController::class, 'store']);
    Route::patch(
        '/admin/students/{studentId}/subjects/{subjectId}',
        [StudentSubjectStatusController::class, 'update']
    );
    Route::post(
        '/admin/prospects/{prospectId}/register-student',
        [ProspectRegistrationController::class, 'registerStudent']
    );
});
