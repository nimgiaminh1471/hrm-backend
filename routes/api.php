<?php

use App\Http\Controllers\Api\JobPostingController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    // Job Postings
    Route::apiResource('job-postings', JobPostingController::class);
    Route::patch('job-postings/{jobPosting}/status', [JobPostingController::class, 'updateStatus']);

    // Candidates
    Route::apiResource('candidates', CandidateController::class);
    Route::patch('candidates/{candidate}/status', [CandidateController::class, 'updateStatus']);

    // Interviews
    Route::apiResource('interviews', InterviewController::class);
    Route::patch('interviews/{interview}/status', [InterviewController::class, 'updateStatus']);
    Route::post('interviews/{interview}/feedback', [InterviewController::class, 'submitFeedback']);

    // Departments
    Route::apiResource('departments', DepartmentController::class);
    Route::patch('departments/{department}/toggle-active', [DepartmentController::class, 'toggleActive']);
    Route::get('departments/{department}/hierarchy', [DepartmentController::class, 'hierarchy']);

    // Positions
    Route::apiResource('positions', PositionController::class);
    Route::patch('positions/{position}/toggle-active', [PositionController::class, 'toggleActive']);

    // Employees
    Route::apiResource('employees', EmployeeController::class);
    Route::patch('employees/{employee}/toggle-active', [EmployeeController::class, 'toggleActive']);
});
