<?php

use App\Http\Controllers\Api\ExamManageController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::middleware('auth:sanctum')->get('/logout', function (Request $request) {
    $user = $request->user();
    $user->tokens()->delete();
    Auth::guard('web')->logout();

    return ['status' => true, 'message' => 'Logout Successful!'];
});

Route::controller(UserAuthController::class)->prefix('/auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/verify-otp', 'verifyOtp');
    Route::post('/login', 'login');
    Route::post('/store-forgot-password', 'storeForgotPassword');
    Route::post('/reset-password', 'resetPassword');
    Route::post('/resend-otp', 'resendOTP');
});

Route::middleware('auth:sanctum')->controller(UserProfileController::class)->prefix('/profile')->group(function () {
    Route::get('/user', 'user');
    Route::post('/update', 'update');
});

Route::middleware('auth:sanctum')->controller(ExamManageController::class)->prefix('/exam')->group(function () {
    Route::post('/check-live-exam', 'checkLiveExam');
    Route::post('/routine', 'routine');
    Route::post('/archive', 'archive');
    Route::post('/syllabus', 'syllabus');
    Route::post('/archive-exam-question-details', 'archiveExamQuestionDetails');
    Route::post('/toggle-favorite', 'toggleFavorite');
    Route::post('/favorite-list', 'favoriteList');
});
