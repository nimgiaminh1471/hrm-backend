<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
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

// Public routes with organization subdomain
Route::prefix('{subdomain}')->middleware('organization')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

// Organization-specific routes
Route::prefix('{subdomain}')->middleware(['organization', 'auth:sanctum'])->group(function () {
    // Department routes
    Route::apiResource('departments', DepartmentController::class);
    
    // Team routes
    Route::apiResource('teams', TeamController::class);
    Route::post('teams/{team}/members', [TeamController::class, 'addMember']);
    Route::delete('teams/{team}/members', [TeamController::class, 'removeMember']);

    // User/Employee routes
    Route::apiResource('users', UserController::class);
    Route::get('users/employee-id/{employeeId}', [UserController::class, 'getByEmployeeId']);
    Route::patch('users/{user}/status', [UserController::class, 'updateStatus']);

    // Position routes
    Route::apiResource('positions', PositionController::class);
    Route::get('departments/{department}/positions', [PositionController::class, 'getByDepartment']);
    Route::get('positions/active', [PositionController::class, 'getActive']);

    // Contract routes
    Route::apiResource('contracts', ContractController::class);
    Route::get('users/{user}/contracts', [ContractController::class, 'getUserContracts']);
    Route::get('contracts/active', [ContractController::class, 'getActive']);
    Route::post('contracts/{contract}/sign', [ContractController::class, 'sign']);
    Route::post('contracts/{contract}/terminate', [ContractController::class, 'terminate']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
