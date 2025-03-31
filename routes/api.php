<?php

use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\JobPostingController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\LeaveTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    // Candidate routes
    Route::apiResource('candidates', CandidateController::class);
    Route::patch('candidates/{candidate}/status', [CandidateController::class, 'updateStatus']);

    // Job Posting routes
    Route::apiResource('job-postings', JobPostingController::class);
    Route::patch('job-postings/{jobPosting}/status', [JobPostingController::class, 'updateStatus']);
    Route::patch('job-postings/{jobPosting}/toggle-active', [JobPostingController::class, 'toggleActive']);

    // Department routes
    Route::apiResource('departments', DepartmentController::class);
    Route::patch('departments/{department}/toggle-active', [DepartmentController::class, 'toggleActive']);
    Route::get('departments/{department}/hierarchy', [DepartmentController::class, 'hierarchy']);

    // Interview routes
    Route::apiResource('interviews', InterviewController::class);
    Route::patch('interviews/{interview}/status', [InterviewController::class, 'updateStatus']);
    Route::post('interviews/{interview}/feedback', [InterviewController::class, 'submitFeedback']);
    Route::post('interviews/{interview}/reschedule', [InterviewController::class, 'reschedule']);

    // Leave Request routes
    Route::apiResource('leave-requests', LeaveRequestController::class);
    Route::post('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve']);
    Route::post('leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject']);
    Route::post('leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel']);

    // Leave Type routes
    Route::apiResource('leave-types', LeaveTypeController::class);
    Route::patch('leave-types/{leaveType}/toggle-active', [LeaveTypeController::class, 'toggleActive']);
    Route::patch('leave-types/{leaveType}/toggle-paid', [LeaveTypeController::class, 'togglePaid']);
}); 