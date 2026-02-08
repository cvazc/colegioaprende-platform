<?php

use App\Enums\EmployeeAbility;
use App\Http\Controllers\Api\Admin\StudentLookupController;
use App\Http\Controllers\Api\Admin\StudentSubjectStatusController;
use App\Http\Controllers\Api\Admin\ProspectRegistrationController;
use App\Http\Controllers\Api\Admin\ProspectController as AdminProspectController;
use App\Http\Controllers\Api\Admin\PaymentReconcileController;
use App\Http\Controllers\Api\Admin\EmployeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\CashPaymentController;
use App\Http\Controllers\Api\Admin\CalendarTemplateController;
use App\Http\Controllers\Api\Admin\StudentCalendarAssignmentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentItemController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\ProspectController;
use App\Http\Controllers\Api\StudentCalendarController;
use App\Http\Controllers\Api\StudentCreditController;
use App\Http\Controllers\Api\StudentExamAttemptController;
use App\Http\Controllers\Api\StudentPaymentController;
use App\Http\Controllers\Api\StudentProgressController;
use App\Http\Controllers\Api\Student\StudentOnboardingController;
use App\Http\Controllers\Api\StudentSubjectController;
use App\Http\Controllers\Api\StudentSubjectUnlockController;
use App\Http\Controllers\Api\Admin\QuestionBankController;
use App\Http\Controllers\Api\Admin\SubjectExamConfigController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/prospects', [ProspectController::class, 'store']);
Route::get('/payment-items', [PaymentItemController::class, 'index']);
Route::post('/prospects/{prospectId}/payments', [PaymentController::class, 'store']);
Route::post('/payments/webhooks/{provider}', [PaymentWebhookController::class, 'handle']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/student/onboarding', [StudentOnboardingController::class, 'update']);

    Route::middleware('student.onboarding')->group(function () {
        Route::get('/student/subjects', [StudentSubjectController::class, 'index']);
        Route::get('/student/progress', [StudentProgressController::class, 'show']);
        Route::post('/student/subjects/{subjectId}/unlock', [StudentSubjectUnlockController::class, 'store']);
        Route::post('/student/subjects/{subjectId}/exam-attempts', [StudentExamAttemptController::class, 'store']);
        Route::get('/student/exam-attempts/{attemptId}', [StudentExamAttemptController::class, 'show']);
        Route::post('/student/exam-attempts/{attemptId}/answers', [StudentExamAttemptController::class, 'submit']);
        Route::get('/student/payments', [StudentPaymentController::class, 'index']);
        Route::get('/student/credits', [StudentCreditController::class, 'show']);
        Route::get('/student/calendar', [StudentCalendarController::class, 'show']);
    });

    Route::middleware('employee.can:' . EmployeeAbility::ManageEmployees->value)->group(function () {
        Route::get('/admin/employees', [EmployeeController::class, 'index']);
        Route::post('/admin/employees', [EmployeeController::class, 'store']);
        Route::patch('/admin/employees/{employeeId}', [EmployeeController::class, 'update']);
    });

    Route::middleware('employee.can:' . EmployeeAbility::ManageProspects->value)->group(function () {
        Route::get('/admin/prospects', [AdminProspectController::class, 'index']);
        Route::post('/admin/prospects/{prospectId}/register-student', [ProspectRegistrationController::class, 'registerStudent']);
    });

    Route::middleware('employee.can:' . EmployeeAbility::ManageStudents->value)->group(function () {
        Route::get('/admin/students/by-email', [StudentLookupController::class, 'byEmail']);
        Route::patch('/admin/students/{studentId}/subjects/{subjectId}', [StudentSubjectStatusController::class, 'update']);
    });

    Route::middleware('employee.can:' . EmployeeAbility::ManageCalendars->value)->group(function () {
        Route::get('/admin/calendar-templates', [CalendarTemplateController::class, 'index']);
        Route::post('/admin/calendar-templates', [CalendarTemplateController::class, 'store']);
        Route::put('/admin/calendar-templates/{templateId}', [CalendarTemplateController::class, 'update']);
        Route::post('/admin/students/{studentId}/calendar-assignment', [StudentCalendarAssignmentController::class, 'assign']);
    });

    Route::middleware('employee.can:' . EmployeeAbility::ManagePayments->value)->group(function () {
        Route::post('/admin/prospects/{prospectId}/payments/cash', [CashPaymentController::class, 'store']);
        Route::post('/admin/payments/{paymentId}/reconcile', [PaymentReconcileController::class, 'store']);
    });

    Route::middleware('employee.can:' . EmployeeAbility::ManageSubjects->value)->group(function () {
        Route::get('/admin/subjects/{subjectId}/questions', [QuestionBankController::class, 'index']);
        Route::post('/admin/subjects/{subjectId}/questions', [QuestionBankController::class, 'store']);
        Route::get('/admin/subjects/{subjectId}/exam-config', [SubjectExamConfigController::class, 'show']);
        Route::put('/admin/subjects/{subjectId}/exam-config', [SubjectExamConfigController::class, 'update']);
    });
});
