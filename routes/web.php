<?php

use App\Http\Controllers\Backend\AdminAuthenticationController;
use App\Http\Controllers\Backend\AdminManagementController;
use App\Http\Controllers\Backend\CompanyInfoController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ExamController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\SubjectController;
use App\Http\Controllers\Backend\TopicSourceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::controller(AdminAuthenticationController::class)->middleware('guest:admin')->group(function () {

    Route::get('/', 'login')->name('login');
    Route::post('/store-login', 'storeLogin')->name('storeLogin');
    Route::get('/forgot-password', 'forgotPassword')->name('forgotPassword');
    Route::post('/store-forgot-password', 'storeForgotPassword')->name('storeForgotPassword');
    Route::get('/reset-password/{token}', 'resetPassword')->name('resetPassword');
    Route::post('/store-reset-password', 'storeResetPassword')->name('storeResetPassword');
});

Route::middleware('auth:admin')->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::post('/logout', 'logout')->name('logout');
    });

    Route::controller(AdminManagementController::class)->prefix('/admin')->name('admin.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{admin}', 'edit')->name('edit');
        Route::put('/update/{admin}', 'update')->name('update');
        Route::delete('/delete/{admin}', 'delete')->name('delete');
    });

    Route::controller(SubjectController::class)->prefix('/subject')->name('subject.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{subject}', 'edit')->name('edit');
        Route::put('/update/{subject}', 'update')->name('update');
        Route::delete('/delete/{subject}', 'delete')->name('delete');
    });

    Route::controller(TopicSourceController::class)->prefix('/topic-source')->name('topic.source.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{topic_source}', 'edit')->name('edit');
        Route::put('/update/{topic_source}', 'update')->name('update');
        Route::delete('/delete/{topic_source}', 'delete')->name('delete');
    });

    Route::controller(ExamController::class)->prefix('/exam')->name('exam.')->group(function () {
        /**
         * preliminary
         */
        Route::get('/index', 'index')->name('index');
        Route::get('/create/{exam_id?}', 'create')->name('create');
        Route::any('/store-or-update/{exam_id?}', 'storeOrUpdate')->name('storeOrUpdate');

        Route::get('/mcq-question/{exam_id}', 'mcqQuestion')->name('mcqQuestion');
        Route::post('/create-or-update-mcq-question/{exam_id}', 'createOrUpdateMCQQuestion')->name('createOrUpdateMCQQuestion');
        Route::get('/delete-question/{question_id}', 'deleteQuestion')->name('deleteQuestion');

        /**
         * written
         */
        Route::get('/written', 'written')->name('written');
        Route::get('/written-create/{exam_id?}', 'writtenCreate')->name('writtenCreate');
        Route::any('/written-store-or-update/{exam_id?}', 'writtenStoreOrUpdate')->name('writtenStoreOrUpdate');

        Route::get('/written-question/{exam_id}', 'writtenQuestion')->name('writtenQuestion');
        Route::post('/create-or-update-written-question/{exam_id}', 'createOrUpdateWrittenQuestion')->name('createOrUpdateWrittenQuestion');
        Route::get('/delete-written-question/{question_id}', 'deleteWrittenQuestion')->name('deleteQuestion');

        Route::post('/get-topic', 'getTopic')->name('getTopic'); //ajax request
    });

    Route::get('/company-info', [CompanyInfoController::class, 'showCompanyInfo'])->name('showCompanyInfo');
    Route::post('/company-info', [CompanyInfoController::class, 'storeCompanyInfo'])->name('storeCompanyInfo');

    Route::controller(PageController::class)->prefix('/page')->name('page.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{page}', 'edit')->name('edit');
        Route::put('/update/{page}', 'update')->name('update');
        Route::delete('/delete/{page}', 'delete')->name('delete');
    });
});
